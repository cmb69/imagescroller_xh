<?php


function imagescroller_images_size($imgs) {
    global $e, $adm;

    foreach ($imgs as $img) {
	$fn = is_array($img) ? $img[0] : $img;
	$size = getimagesize($fn);
	if (!isset($width)) {
	    list($width, $height) = $size;
	} else {
	    if (($size[0] != $width || $size[1] != $height) && $adm) {
		$e .= '<li><strong>IMAGE SIZE ERROR</strong></li>'."\n"; // TODO
	    }
	}
    }
    return array($imgs, $width, $height);
}


/**
 * Returns the sorted list of images in $dir.
 *
 * @global string $e
 * @param string $dir
 * @return array
 */
function imagescroller_images($dir) {
    $imgs = array();
    if (($dh = opendir($dir)) !== FALSE) {
	while (($fn = readdir($dh)) !== FALSE) {
	    $ffn = $dir.$fn;
	    if (is_file($ffn) && getimagesize($ffn)) {
		$imgs[] = $ffn;
	    }
	}
	closedir($dh);
    }
    natcasesort($imgs);
    return imagescroller_images_size($imgs);
}


function imagescroller_images_from_file($fn) {
    $dir = dirname($fn).'/';
    //$lines = file($fn);
    //var_dump($lines);
    //$recs = array(); $rec = array();
//    foreach ($lines as $line) {
//	$line = rtrim($line);
//	if (empty($line)) {
//	    $recs[] = $rec; $rec = array();
//	} else {
//	    $rec[] = (empty($rec) ? $dir.'/' : '').$line;
//	}
//    }
//    var_dump($recs);
    $data = file_get_contents($fn);
    $data = str_replace(array("\r\n", "\r"), "\n", $data);
    $recs = explode("\n\n", $data);
    foreach ($recs as $rec) {
	$rec = explode("\n", $rec);
	$rec[0] = $dir.$rec[0];
	$res[] = $rec;
    }
    return imagescroller_images_size($res);
}

/**
 * Includes the necessary JS.
 *
 //* @global $hjs
 * @return void
 */
function imagescroller_js() { // TODO: template call
    global $pth, $hjs, $plugin_cf;

    $pcf = $plugin_cf['imagescroller'];
    include_once $pth['folder']['plugins'].'jquery/jquery.inc.php';
    include_jquery();
    include_jqueryplugin('scrollTo', $pth['folder']['plugins'].'imagescroller/lib/jquery.scrollTo-1.4.2-min.js');
    include_jqueryplugin('serialScroll', $pth['folder']['plugins'].'imagescroller/lib/jquery.serialScroll-1.2.2-min.js');
    $hjs .= <<<SCRIPT
<script type="text/javascript">
/* <![CDATA[ */
jQuery(function() {
    jQuery('#imagescroller').serialScroll({
	items: 'li',
	prev: '#imagescroller_container img.imagescroller_prev',
	next: '#imagescroller_container img.imagescroller_next',
	force: true,
	axis: 'xy',
	duration: $pcf[scroll_duration],
	interval: $pcf[scroll_interval]
    });
    jQuery('#imagescroller_container').mouseenter(function() {jQuery('img.imagescroller_prev,img.imagescroller_next,img.imagescroller_play,img.imagescroller_stop').show()})
	    .mouseleave(function() {jQuery('img.imagescroller_prev,img.imagescroller_next,img.imagescroller_play,img.imagescroller_stop').hide()});
    jQuery('img.imagescroller_stop').click(function() {
	jQuery('#imagescroller').trigger('stop');
	jQuery(this).css('visibility', 'hidden');
	jQuery('img.imagescroller_play').css('visibility', 'visible');
    });
    jQuery('img.imagescroller_play').click(function() {
	jQuery('#imagescroller').trigger('start');
	jQuery(this).css('visibility', 'hidden');
	jQuery('img.imagescroller_stop').css('visibility', 'visible');
    })
})
/* ]]> */
</script>

SCRIPT;

}

function imagescroller($dir) {
    global $pth;

    //$dir .= '/'; // TODO: general
    list($imgs, $width, $height) = is_dir($dir) ? imagescroller_images($dir) : imagescroller_images_from_file($dir);
    imagescroller_js();
    $o = '<div id="imagescroller_container" style="width: '.$width.'px; height: '.$height.'px">'."\n"
	    .'<div id="imagescroller" style="width: '.$width.'px; height: '.$height.'px">'."\n"
	    .'<ul style="width: '.count($imgs) * $width.'px; height: '.$height.'px">'."\n";
    foreach ($imgs as $img) {
	if (is_array($img)) {
	    list($fn, $url, $title, $desc) = $img;
	} else {
	    $fn = $img; $url = $title = $desc = NULL;
	}
	$o .= '<li>'
		.(!is_null($url) ? '<a href="'.$url.'">' : '')
		.tag('img src="'.$fn.'" alt="" width="'.$width.'" height="'.$height.'"')
		.(!is_null($url) ? '</a>' : '');
	if (!is_null($title) || !is_null($desc)) {
	    $o .= '<div class="imagescroller_info">'
		    .'<h6><a href="'.$url.'">'.$title.'</a></h6>'
		    .'<p>'.$desc.'</p>'
		    .'</div>'."\n";
	}
	$o .= '</li>'."\n";
    }
    $o .= '</ul>'."\n";
    $o .= '</div>'."\n";
    foreach (array('prev', 'next', 'play', 'stop') as $btn) {
	//$name = $btn == 'prev' ? 'left' : ($btn == 'next' ? 'right' : $btn);
	$name = $btn;
	$img = $pth['folder']['plugins'].'imagescroller/images/'.$name.'.png';
	list($w, $h) = getimagesize($img);
	$top = 'top:'.intval(($height - $h) / 2).'px;';
	$left = $btn == 'play' || $btn == 'stop' ? 'left:'.intval(($width - $w) / 2).'px' : '';
	$o .= tag('img class="imagescroller_'.$btn.'" src="'.$img.'" alt="'.$btn.'"'
		.' style="'.$top.$left.'"');
    }
    $o .= '</div>'."\n";
    return $o;
}

?>
