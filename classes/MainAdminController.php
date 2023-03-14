<?php

/*
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

class MainAdminController
{
    /**
     * @return void
     */
    public function defaultAction()
    {
        echo $this->gallerySelectbox();
        $this->editAction();
    }

    /**
     * @return void
     */
    public function editAction()
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
        echo $o;
    }

    /**
     * @return void
     */
    public function saveAction()
    {
        $gallery = array();
        foreach (array_keys($_POST['imagescroller_image']) as $i) {
            $image = array();
            foreach (array('image', 'title', 'desc', 'link') as $key) {
                $image[$key] = $_POST["imagescroller_$key"][$i];
            }
            $gallery[] = $image;
        }
        // var_dump($gallery);
    }

    /**
     * @return string
     */
    private function gallerySelectbox()
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

    /** @return list<string> */
    private function galleries()
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
}
