<?php

/**
 * Front-End of Imagescroller_XH.
 *
 * Copyright (c) 2012 Christoph M. Becker (see license.txt)
 */


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


define('IMAGESCROLLER_VERSION', '1beta3');


/**
 * Returns the sorted array of images in $dir.
 *
 * @param string $dir
 * @return array
 */
function Imagescroller_imagesFromDir($dir)
{
    $dir = rtrim($dir, '/') . '/';
    $imgs = array();
    if (($dh = opendir($dir)) !== FALSE) {
        while (($fn = readdir($dh)) !== FALSE) {
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
 * Returns the array of images in the info file $fn.
 *
 * @param string $fn
 * @return array
 */
function Imagescroller_imagesFromFile($fn)
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
 * If the dimensions differ, this will be reported through $e in admin mode.
 *
 * @global string $e
 * @param array $images
 * @returns array
 */
function Imagescroller_imagesSize($imgs)
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
                    . sprintf($ptx['error_image_size'],
                              $size[0], $size[1], $width, $height)
                    . '</strong>' . tag('br') . "$fn</li>";
            }
        }
    }
    return array($width, $height);
}


/**
 * Returns the <li> containing the image.
 *
 * @param mixed $img
 * @param int $width
 * @param int $height
 * @return string  The (X)HTML.
 */
function Imagescroller_imageLi($img, $width, $height)
{
    if (is_array($img)) {
        list($fn, $url, $title, $desc) = $img;
    } else {
        $fn = $img; $url = $title = $desc = NULL;
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
 * @access public
 * @global $hjs
 * @return void
 */
function Imagescroller_js()
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
    include_jqueryplugin('scrollTo', $pth['folder']['plugins']
        . 'imagescroller/lib/jquery.scrollTo-1.4.3.1-min.js');
    include_jqueryplugin('serialScroll', $pth['folder']['plugins']
        . 'imagescroller/lib/jquery.serialScroll-1.2.2-min.js');
    $dynctrls = $pcf['controls_dynamic'] ? 'true' : 'false';
    $hjs .= <<<SCRIPT
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
            constant: false
        });
        if ($dynctrls) {
            $('div.imagescroller_container').mouseenter(function() {
                $(this).find('img.imagescroller_prev, img.imagescroller_next,
			      img.imagescroller_play, img.imagescroller_stop').show();
            }).mouseleave(function() {
                $(this).find('img.imagescroller_prev, img.imagescroller_next,
			      img.imagescroller_play, img.imagescroller_stop').hide();
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

SCRIPT;
}


/**
 * Returns the imagescroller for the images in $path.
 *
 * @access public
 * @param string $path  A directory of info file.
 * @return string  The (X)HTML.
 */
function imagescroller($path)
{
    global $pth, $plugin_tx;

    $imgs = is_dir($path)
	? imagescroller_imagesFromDir($path)
	: imagescroller_imagesFromFile($path);
    list($width, $height) = Imagescroller_imagesSize($imgs);
    Imagescroller_js();
    $o = "<div class=\"imagescroller_container\" style=\"width:${width}px; height:${height}px\">"
        . "<div class=\"imagescroller\" style=\"width:${width}px; height:${height}px\">"
        . '<ul style="width:' . count($imgs) * $width . "px; height:${height}px\">";
    foreach ($imgs as $img) {
        $o .= Imagescroller_imageLi($img, $width, $height);
    }
    $o .= '</ul>';
    $o .= '</div>';
    foreach (array('prev', 'next', 'play', 'stop') as $btn) {
        $name = $btn;
        $alt = $plugin_tx['imagescroller']["button_$btn"];
        $img = "{$pth['folder']['plugins']}imagescroller/images/$name.png";
        list($w, $h) = getimagesize($img);
        $top = 'top:' . intval(($height - $h) / 2) . 'px;';
        $left = $btn == 'play' || $btn == 'stop' ? 'left:' . intval(($width - $w) / 2) . 'px' : '';
        $o .= tag("img class=\"imagescroller_$btn\" src=\"$img\" alt=\"$alt\""
		  . " style=\"$top$left\"");
    }
    $o .= '</div>';
    return $o;
}


/**
 * Handle autoloading of necessary JS.
 */
if ($plugin_cf['imagescroller']['autoload']) {
    Imagescroller_js();
}

?>
