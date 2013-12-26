<?php

/**
 * Back-End of Imagescroller_XH.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Imagescroller
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2013 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Imagescroller_XH
 */

/*
 * Prevent direct access.
 */
if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
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
function Imagescroller_version()
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
<p>Copyright &copy; 2012-2013 <a href="http://3-magi.net">
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
function Imagescroller_systemCheck()
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
function Imagescroller_galleries()
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
function Imagescroller_gallerySelectbox()
{
    global $sn;

    $onchange = "window.document.location.href = '$sn?&imagescroller"
        . "&amp;admin=plugin_main&amp;imagescroller_gallery='+this.value";
    $o = "<select onchange=\"$onchange\">";
    $galleries = Imagescroller_galleries();
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
function Imagescroller_galleryAdmin()
{
    global $sn;

    $o = Imagescroller_gallerySelectbox();
    $o .= Imagescroller_editGallery();
    return $o;
}

/**
 * Returns the edit gallery view.
 *
 * @return string (X)HTML.
 *
 * @global array The paths of system files and folders.
 */
function Imagescroller_editGallery()
{
    global $pth, $sn;

    $dn = "{$pth['folder']['images']}$_GET[imagescroller_gallery]";
    $imgs = Imagescroller_imagesFromDir("$dn/");
    $url = "$sn?imagescroller&amp;admin=plugin_main";
    $o = "<form action=\"$url\" method=\"POST\"><table><tbody>";
    foreach ($imgs as $img) {
        $o .= '<tr><td>' . tag("img src=\"$img\" width=\"200\" height=\"\" alt=\"\"")
            . tag(
                "input type=\"hidden\" name=\"imagescroller_image[]\" value=\"$img\""
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
function Imagescroller_saveGallery()
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

/**
 * Handle the plugin administration.
 */
if (isset($imagescroller) && $imagescroller == 'true') {
    $o .= print_plugin_admin('on');
    switch ($admin) {
    case '':
        $o .= Imagescroller_version() . tag('hr') . Imagescroller_systemCheck();
        break;
    case 'plugin_main':
        switch ($action) {
        case 'edit_gallery':
            $o .= Imagescroller_editGallery();
            break;
        case 'save':
            $o .= Imagescroller_saveGallery();
            break;
        default:
            $o .= Imagescroller_galleryAdmin();
        }
        break;
    default:
        $o .= plugin_admin_common($action, $admin, $plugin);
    }
}

?>
