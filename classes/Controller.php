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
        $libraryFolder =  $pth['folder']['plugins'] . 'imagescroller/lib/';
        include_jqueryplugin(
            'scrollTo', $libraryFolder . 'jquery.scrollTo-1.4.3.1-min.js'
        );
        include_jqueryplugin(
            'serialScroll', $libraryFolder . 'jquery.serialScroll-1.2.2-min.js'
        );
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

        $gallery = is_dir($path)
            ? Imagescroller_Gallery::makeFromFolder($path)
            : Imagescroller_Gallery::makeFromFile($path);
        list($width, $height) = $gallery->getDimensions();
        $this->emitJs();
        $totalWidth = $gallery->getImageCount() * $width;
        return $this->render(
            'gallery', compact('gallery', 'width', 'height', 'totalWidth')
        );
    }

    /**
     * Renders a template.
     *
     * @param string $template A template name.
     * @param array  $bag      A bag with template variables.
     *
     * @return string (X)HTML.
     *
     * @global array The paths of system files and folders.
     * @global array The configuration of the core.
     */
    protected function render($template, $bag)
    {
        global $pth, $cf;

        ob_start();
        extract($bag);
        include $pth['folder']['plugins'] . 'imagescroller/views/' . $template
            . '.htm';
        $html = ob_get_clean();
        if (!$cf['xhtml']['endtags']) {
            $html = str_replace(' />', '>', $html);
        }
        return $html;
    }

    /**
     * Renders the buttons.
     *
     * @param int $width  A width.
     * @param int $height A height.
     *
     * @return string (X)HTML.
     *
     * @global array The paths of system files and folders.
     * @global array The localization of the plugins.
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
     * Returns the plugin version information view.
     *
     * @return string (X)HTML.
     *
     * @global array The paths of system files and folders.
     */
    protected function version()
    {
        global $pth;

        $iconPath = $pth['folder']['plugins'] . 'imagescroller/imagescroller.png';
        $version = IMAGESCROLLER_VERSION;
        return $this->render('info', compact('iconPath', 'version'));
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
