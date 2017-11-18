<?php

/**
 * Copyright 2012-2017 Christoph M. Becker
 *
 * This file is part of Imagescroller_XH.
 *
 * Imagescroller_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Imagescroller_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Imagescroller_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Imagescroller;

use Pfw\SystemCheckService;
use Pfw\View\View;
use Pfw\View\HtmlString;

class Controller
{
    /**
     * @return void
     */
    public function dispatch()
    {
        global $plugin_cf;

        if ($plugin_cf['imagescroller']['autoload']) {
            $this->emitJs();
        }
        if (XH_ADM) {
            if (XH_wantsPluginAdministration('imagescroller')) {
                $this->handleAdministration();
            }
        }
    }

    /**
     * @return void
     */
    protected function handleAdministration()
    {
        global $admin, $action, $o;

        $o .= print_plugin_admin('on');
        switch ($admin) {
            case '':
                ob_start();
                (new InfoController)->defaultAction();
                $o .= ob_get_clean();
                break;
            case 'plugin_main':
                switch ($action) {
                    case 'edit_gallery':
                        $o .= $this->editGallery();
                        break;
                    case 'save':
                        $o .= $this->saveGallery();
                        break;
                    default:
                        $o .= $this->galleryAdmin();
                }
                break;
            default:
                $o .= plugin_admin_common($action, $admin, 'imagescroller');
        }
    }

    /**
     * @return void
     */
    public function emitJs()
    {
        global $pth, $hjs, $plugin_cf;
        static $again = false;

        if ($again) {
            return;
        }
        $again = true;
        $pcf = $plugin_cf['imagescroller'];
        include_once $pth['folder']['plugins'] . 'jquery/jquery.inc.php';
        include_jquery();
        $libraryFolder =  $pth['folder']['plugins'] . 'imagescroller/lib/';
        include_jqueryplugin('scrollTo', $libraryFolder . 'jquery.scrollTo-1.4.3.1-min.js');
        include_jqueryplugin('serialScroll', $libraryFolder . 'jquery.serialScroll-1.2.2-min.js');
        $config = array(
            'duration' => (int) $pcf['scroll_duration'],
            'interval' => (int) $pcf['scroll_interval'],
            'constant' => (bool) $pcf['rewind_fast'],
            'dynamicControls' => (bool) $pcf['controls_dynamic']
        );
        $hjs .= '<script type="text/javascript">/* <![CDATA[ */'
            . 'var IMAGESCROLLER = ' . XH_encodeJson($config) . ';'
            . '/* ]]> */</script>'
            . '<script type="text/javascript" src="' . $pth['folder']['plugins']
            . 'imagescroller/imagescroller.js"></script>';
    }

    /**
     * @param string $path
     * @return string
     */
    public function main($path)
    {
        global $pth, $plugin_tx;

        if (is_dir($path)) {
            $gallery = Gallery::makeFromFolder($path);
        } elseif (is_file($path)) {
            $gallery = Gallery::makeFromFile($path);
        } else {
            return XH_message('fail', $plugin_tx['imagescroller']['error_gallery_missing'], $path);
        }
        list($width, $height) = $gallery->getDimensions();
        $this->emitJs();
        $totalWidth = $gallery->getImageCount() * $width;
        $renderedButtons = new HtmlString($this->renderButtons($width, $height));
        ob_start();
        (new View('imagescroller'))
            ->template('gallery')
            ->data(compact('gallery', 'width', 'height', 'totalWidth', 'renderedButtons'))
            ->render();
        return ob_get_clean();
    }

    /**
     * @param int $width
     * @param int $height
     * @return string
     */
    protected function renderButtons($width, $height)
    {
        global $pth, $plugin_tx;

        $html = '';
        foreach (array('prev', 'next', 'play', 'stop') as $btn) {
            $name = $btn;
            $alt = $plugin_tx['imagescroller']['button_' . $btn];
            $img = $pth['folder']['plugins'] . 'imagescroller/images/' . $name
                . '.png';
            list($w, $h) = getimagesize($img);
            $top = 'top:' . intval(($height - $h) / 2) . 'px;';
            $left = ($btn == 'play' || $btn == 'stop')
                ? 'left:' . intval(($width - $w) / 2) . 'px'
                : '';
            $html .= tag(
                'img class="imagescroller_' . $btn . '" src="' . $img
                . '" alt="' . $alt . '"' . ' style="' . $top . $left . '"'
            );
        }
        return $html;
    }

    /**
     * @return array
     */
    protected function galleries()
    {
        global $pth;

        $galleries = array();
        $dh = opendir($pth['folder']['images']);
        while (($fn = readdir($dh)) !== false) {
            if ($fn{0} != '.' && is_dir("{$pth['folder']['images']}$fn")) {
                $galleries[] = $fn;
            }
        }
        closedir($dh);
        natcasesort($galleries);
        return $galleries;
    }

    /**
     * @return string
     */
    protected function gallerySelectbox()
    {
        global $sn;

        $onchange = "window.document.location.href = '$sn?&imagescroller"
            . "&amp;admin=plugin_main&amp;imagescroller_gallery='+this.value";
        $o = "<select onchange=\"$onchange\">";
        $galleries = $this->galleries();
        foreach ($galleries as $gallerie) {
            $sel = (isset($_GET['imagescroller_gallery'])
                && $gallerie == $_GET['imagescroller_gallery'])
                    ? ' selected="selected"'
                    : '';
            $o .= "<option value=\"$gallerie\"$sel>$gallerie</option>";
        }
        $o .= '</select>';
        return $o;
    }

    /**
     * @return string
     */
    protected function galleryAdmin()
    {
        global $sn;

        $o = $this->gallerySelectbox();
        $o .= $this->editGallery();
        return $o;
    }

    /**
     * @return string
     */
    protected function editGallery()
    {
        global $pth, $sn;

        $dn = "{$pth['folder']['images']}$_GET[imagescroller_gallery]";
        $gallery = Gallery::makeFromFolder("$dn/");
        $url = "$sn?imagescroller&amp;admin=plugin_main";
        $o = "<form action=\"$url\" method=\"POST\"><table><tbody>";
        foreach ($gallery->getImages() as $img) {
            $o .= '<tr><td>'
                . tag("img src=\"{$img->getFilename()}\" width=\"200\" height=\"\" alt=\"\"")
                . tag(
                    'input type="hidden" name="imagescroller_image[]" value="'
                    . $img->getFilename() . '"'
                )
                . '</td>'
                . '<td>'
                . tag("input type=\"text\" name=\"imagescroller_title[]\"")
                . tag("input type=\"text\" name=\"imagescroller_desc[]\"")
                . tag("input type=\"text\" name=\"imagescroller_link[]\"")
                . '</td>'
                . '</tr>';
        }
        $o .= '</tbody></table>'
            . tag('input type="hidden" name="action" value="save"')
            . tag('input type="submit" class="submit"') . '</form>';
        return $o;
    }

    /**
     * @return string
     */
    protected function saveGallery()
    {
        $gallery = array();
        foreach (array_keys($_POST['imagescroller_image']) as $i) {
            $image = array();
            foreach (array('image', 'title', 'desc', 'link') as $key) {
                $image[$key] = $_POST["imagescroller_$key"][$i];
            }
            $gallery[] = $image;
        }
        var_dump($gallery);
    }
}
