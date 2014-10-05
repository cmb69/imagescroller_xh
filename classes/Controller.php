<?php

/**
 * The controller.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Imagescroller
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2014 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Schedule_XH
 */

/**
 * The controller.
 *
 * @category CMSimple_XH
 * @package  Imagescroller
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Schedule_XH
 */
class Imagescroller_Controller
{
    /**
     * Dispatches on plugin related requests.
     *
     * @return void
     *
     * @global array The configuration of the plugins.
     * @global bool  Whether we're logged in as admin.
     */
    public function dispatch()
    {
        global $plugin_cf, $adm;

        if ($plugin_cf['imagescroller']['autoload']) {
            $this->emitJs();
        }
        if ($adm) {
            if ($this->isAdministrationRequested()) {
                $this->handleAdministration();
            }
        }
    }

    /**
     * Returns whether the plugin administration is requested.
     *
     * @return bool
     *
     * @global $imagescroller Whether the plugin administration is requested.
     */
    protected function isAdministrationRequested()
    {
        global $imagescroller;

        return isset($imagescroller) && $imagescroller == 'true';
    }

    /**
     * Handles the plugin administration.
     *
     * @return void
     *
     * @global string The value of the admin GP parameter.
     * @global string The value of the action GP parameter.
     * @global string The (X)HTML of the contents area.
     */
    protected function handleAdministration()
    {
        global $admin, $action, $o;

        $o .= print_plugin_admin('on');
        switch ($admin) {
        case '':
            $o .= $this->version() . tag('hr')
                . $this->systemCheck();
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
     * Returns the sorted array of images in a folder.
     *
     * @param string $dir A folder path.
     *
     * @return array
     */
    protected function imagesFromDir($dir)
    {
        $dir = rtrim($dir, '/') . '/';
        $imgs = array();
        if (($dh = opendir($dir)) !== false) {
            while (($fn = readdir($dh)) !== false) {
                $ffn = $dir . $fn;
                if (is_file($ffn) && getimagesize($ffn)) {
                    $imgs[] = $ffn;
                }
            }
            closedir($dh);
        }
        natcasesort($imgs);
        return $imgs;
    }

    /**
     * Returns the array of images in an info file.
     *
     * @param string $fn An info file name.
     *
     * @return array
     */
    protected function imagesFromFile($fn)
    {
        $dir = dirname($fn) . '/';
        $data = file_get_contents($fn);
        $data = str_replace(array("\r\n", "\r"), "\n", $data);
        $recs = explode("\n\n", $data);
        foreach ($recs as $rec) {
            $rec = array_map('trim', explode("\n", $rec));
            $rec[0] = $dir . $rec[0];
            $res[] = $rec;
        }
        return $res;
    }

    /**
     * Returns the dimensions of the $imgs.
     *
     * If the dimensions differ, this will be reported through $e in admin mode.
     *
     * @param array $imgs A list of images.
     *
     * @return array
     *
     * @global string The (X)HTML containing error messages.
     * @global bool   Whether we're in admin mode.
     * @global array  The localization of the plugins.
     */
    protected function imagesSize($imgs)
    {
        global $e, $adm, $plugin_tx;

        $ptx = $plugin_tx['imagescroller'];
        foreach ($imgs as $img) {
            $fn = is_array($img) ? $img[0] : $img;
            if (!is_readable($fn) || !($size = getimagesize($fn))) {
                $e = "<li><strong>$ptx[error_no_image]</strong>"
                    . tag('br') . "$fn</li>";
                continue;
            }
            if (!isset($width)) {
                list($width, $height) = $size;
            } else {
                if (($size[0] != $width || $size[1] != $height) && $adm) {
                    $e .= '<li><strong>'
                        . sprintf(
                            $ptx['error_image_size'],
                            $size[0], $size[1], $width, $height
                        )
                        . '</strong>' . tag('br') . "$fn</li>";
                }
            }
        }
        return array($width, $height);
    }

    /**
     * Returns the <li> containing the image.
     *
     * @param mixed $img    An image.
     * @param int   $width  An image width.
     * @param int   $height An image height.
     *
     * @return string (X)HTML.
     */
    protected function imageLi($img, $width, $height)
    {
        if (is_array($img)) {
            list($fn, $url, $title, $desc) = $img;
        } else {
            $fn = $img; $url = $title = $desc = null;
        }
        $o = '<li>'
            . (!empty($url) ? "<a href=\"$url\">" : '')
            . tag("img src=\"$fn\" alt=\"\" width=\"$width\" height=\"$height\"")
            . (!empty($url) ? '</a>' : '');
        if (!empty($title) || !empty($desc)) {
            $o .= '<div class="imagescroller_info">'
                . '<h6>'
                . (!empty($url) ? "<a href=\"$url\">" : '')
                . $title // htmlspecialchars?
                . (!empty($url) ? '</a>' : '')
                . '</h6>'
                . "<p>$desc</p>" // Htmlspecialchars?
                . '</div>';
        }
        $o .= '</li>';
        return $o;
    }

    /**
     * Includes the necessary JS.
     *
     * @return void
     *
     * @global array  The paths of system files and folders.
     * @global string The (X)HTML to insert in the HEAD element.
     * @global array  The configuration of the plugins.
     *
     * @staticvar bool $again Whether the function is called again.
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
        include_jqueryplugin(
            'scrollTo', $pth['folder']['plugins']
            . 'imagescroller/lib/jquery.scrollTo-1.4.3.1-min.js'
        );
        include_jqueryplugin(
            'serialScroll', $pth['folder']['plugins']
            . 'imagescroller/lib/jquery.serialScroll-1.2.2-min.js'
        );
        $fastRewind = $pcf['rewind_fast'] ? 'false' : 'true';
        $dynctrls = $pcf['controls_dynamic'] ? 'true' : 'false';
        $hjs .= <<<EOT
<script type="text/javascript">
/* <![CDATA[ */
(function($) {
    $(function() {
        $('div.imagescroller').serialScroll({
            items: 'li',
            prev: 'div.imagescroller_container img.imagescroller_prev',
            next: 'div.imagescroller_container img.imagescroller_next',
            force: true,
            axis: 'xy',
            duration: $pcf[scroll_duration],
            interval: $pcf[scroll_interval],
            constant: $fastRewind
        });
        if ($dynctrls) {
            $('div.imagescroller_container').mouseenter(function() {
                $(this).find('img.imagescroller_prev, img.imagescroller_next,' +
                        'img.imagescroller_play, img.imagescroller_stop').show();
            }).mouseleave(function() {
                $(this).find('img.imagescroller_prev, img.imagescroller_next,' +
                        'img.imagescroller_play, img.imagescroller_stop').hide();
            });
            $('img.imagescroller_stop').click(function() {
                $('div.imagescroller').trigger('stop');
                $('img.imagescroller_stop').css('visibility', 'hidden');
                $('img.imagescroller_play').css('visibility', 'visible');
            });
            $('img.imagescroller_play').click(function() {
                $('div.imagescroller').trigger('start');
                $('img.imagescroller_play').css('visibility', 'hidden');
                $('img.imagescroller_stop').css('visibility', 'visible');
            })
        } else {
            $(this).find('img.imagescroller_prev, img.imagescroller_next').show()
        }
    })
})(jQuery)
/* ]]> */
</script>

EOT;
    }

    /**
     * Returns the imagescroller for the images in $path.
     *
     * @param string $path A directory or info file path.
     *
     * @return string (X)HTML.
     *
     * @global array The paths of system files and folders.
     * @global array The localization of the plugins.
     */
    public function main($path)
    {
        global $pth, $plugin_tx;

        $imgs = is_dir($path)
            ? $this->imagesFromDir($path)
            : $this->imagesFromFile($path);
        list($width, $height) = $this->imagesSize($imgs);
        $this->emitJs();
        $totalWidth = count($imgs) * $width;
        $o = <<<EOT
<div class="imagescroller_container" style="width:{$width}px; height:{$height}px">
    <div class="imagescroller" style="width:{$width}px; height:{$height}px">
        <ul style="width:{$totalWidth}px; height:{$height}px\">

EOT;
        foreach ($imgs as $img) {
            $o .= $this->imageLi($img, $width, $height);
        }
        $o .= '</ul>';
        $o .= '</div>';
        foreach (array('prev', 'next', 'play', 'stop') as $btn) {
            $name = $btn;
            $alt = $plugin_tx['imagescroller']["button_$btn"];
            $img = "{$pth['folder']['plugins']}imagescroller/images/$name.png";
            list($w, $h) = getimagesize($img);
            $top = 'top:' . intval(($height - $h) / 2) . 'px;';
            $left = ($btn == 'play' || $btn == 'stop')
                ? 'left:' . intval(($width - $w) / 2) . 'px'
                : '';
            $o .= tag(
                "img class=\"imagescroller_$btn\" src=\"$img\" alt=\"$alt\""
                . " style=\"$top$left\""
            );
        }
        $o .= '</div>';
        return $o;
    }

    /**
     * Returns the plugin version information view.
     *
     * @return string (X)HTML.
     *
     * @global array The paths of system files and folders.
     *
     * @todo Fix empty element.
     */
    protected function version()
    {
        global $pth;

        $iconPath = $pth['folder']['plugins'] . 'imagescroller/imagescroller.png';
        $version = IMAGESCROLLER_VERSION;
        return <<<EOT
<h1><a href="http://3-magi.net/?CMSimple_XH/Imagescroller_XH">
    Imagescroller_XH</a></h1>
<img src="$iconPath" with="128" height="128"
     style="float: left; margin: 0 1em 0 0" alt="Plugin Icon" />
<p>Version: $version</p>
<p>Copyright &copy; 2012-2014 <a href="http://3-magi.net">
    Christoph M. Becker</a></p>
<p>Imagescroller_XH is powered by <a
    href="http://flesler.blogspot.de/2008/02/jqueryserialscroll.html">
    jQuery.SerialScroll</a>.</p>
<p style="text-align: justify">
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
</p>
<p style="text-align: justify">
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHAN&shy;TABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
</p>
<p style="text-align: justify">
    You should have received a copy of the GNU General Public License
    along with this program. If not, see <a
    href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.
</p>

EOT;
    }

    /**
     * Returns the requirements information view.
     *
     * @return string (X)HTML.
     *
     * @global array The paths of system files and folders.
     * @global array The localization of the core.
     * @global array The localization of the plugins.
     */
    protected function systemCheck()
    {
        global $pth, $tx, $plugin_tx;

        $ptx = $plugin_tx['imagescroller'];
        $phpVersion = '4.3.0';
        $imgdir = $pth['folder']['plugins'] . 'imagescroller/images/';
        $ok = tag('img src="' . $imgdir . 'ok.png" alt="ok"');
        $warn = tag('img src="' . $imgdir . 'warn.png" alt="warning"');
        $fail = tag('img src="' . $imgdir . 'fail.png" alt="failure"');
        $o = '<h4>' . $ptx['syscheck_title'] . '</h4>'
            . (version_compare(PHP_VERSION, $phpVersion) >= 0 ? $ok : $fail)
            . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_phpversion'], $phpVersion)
            . tag('br') . tag('br');
        foreach (array() as $ext) {
            $o .= (extension_loaded($ext) ? $ok : $fail)
                . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_extension'], $ext)
                . tag('br');
        }
        $o .= (!get_magic_quotes_runtime() ? $ok : $fail)
            . '&nbsp;&nbsp;' . $ptx['syscheck_magic_quotes'] . tag('br');
        $o .= (strtoupper($tx['meta']['codepage']) == 'UTF-8' ? $ok : $warn)
            . '&nbsp;&nbsp;' . $ptx['syscheck_encoding'] . tag('br') . tag('br');
        $filename = $pth['folder']['plugins'].'jquery/jquery.inc.php';
        $o .= (file_exists($filename) ? $ok : $fail)
            . '&nbsp;&nbsp;' . $ptx['syscheck_jquery'] . tag('br') . tag('br');
        foreach (array('config/', 'css/', 'languages/') as $folder) {
            $folder = $pth['folder']['plugins'].'imagescroller/' . $folder;
            $o .= (is_writable($folder) ? $ok : $warn)
                . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_writable'], $folder)
                . tag('br');
        }
        return $o;
    }

    /**
     * Returns the available galleries.
     *
     * @return array
     *
     * @global array The paths of system files and folders.
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
     * Returns a gallery select element.
     *
     * @return string (X)HTML.
     *
     * @global string The script name.
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
     * Handles the gallery administration.
     *
     * @return string (X)HTML.
     *
     * @global string The script name.
     */
    protected function galleryAdmin()
    {
        global $sn;

        $o = $this->gallerySelectbox();
        $o .= $this->editGallery();
        return $o;
    }

    /**
     * Returns the edit gallery view.
     *
     * @return string (X)HTML.
     *
     * @global array  The paths of system files and folders.
     * @global string The script name.
     */
    protected function editGallery()
    {
        global $pth, $sn;

        $dn = "{$pth['folder']['images']}$_GET[imagescroller_gallery]";
        $imgs = $this->imagesFromDir("$dn/");
        $url = "$sn?imagescroller&amp;admin=plugin_main";
        $o = "<form action=\"$url\" method=\"POST\"><table><tbody>";
        foreach ($imgs as $img) {
            $o .= '<tr><td>'
                . tag("img src=\"$img\" width=\"200\" height=\"\" alt=\"\"")
                . tag(
                    'input type="hidden" name="imagescroller_image[]" value="'
                    . $img . '"'
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
     * Saves a gallery.
     *
     * @return string (X)HTML.
     */
    protected function saveGallery()
    {
        $gallery = array();
        foreach (array_keys($_POST['imagescroller_image']) as $i) {
            $image = array();
            foreach (array('image', 'title', 'desc', 'link') as $key) {
                $image[$key] = stsl($_POST["imagescroller_$key"][$i]);
            }
            $gallery[] = $image;
        }
        var_dump($gallery);
    }

}

?>
