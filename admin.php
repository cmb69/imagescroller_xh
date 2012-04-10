<?php

/**
 * Back-End of Imagescroller_XH.
 *
 * Copyright (c) 2012 Christoph M. Becker (see license.txt)
 */


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


define('IMAGESCROLLER_VERSION', '1beta3');


/**
 * Returns the plugin version information view.
 *
 * @return string  The (X)HTML.
 */
function imagescroller_version() {
    global $pth;

    return '<h1><a href="http://3-magi.net/?CMSimple_XH/Imagescroller_XH">Imagescroller_XH</a></h1>'."\n"
	    .tag('img src="'.$pth['folder']['plugins'].'imagescroller/imagescroller.png" class="imagescroller_plugin_icon"')
	    .'<p>Version: '.IMAGESCROLLER_VERSION.'</p>'."\n"
	    .'<p>Copyright &copy; 2012 <a href="http://3-magi.net">Christoph M. Becker</a></p>'."\n"
	    .'<p>Imagescroller_XH is powered by <a href="http://flesler.blogspot.de/2008/02/jqueryserialscroll.html">jQuery.SerialScroll</a>.</p>'."\n"
	    .'<p class="imagescroller_license">This program is free software: you can redistribute it and/or modify'
	    .' it under the terms of the GNU General Public License as published by'
	    .' the Free Software Foundation, either version 3 of the License, or'
	    .' (at your option) any later version.</p>'."\n"
	    .'<p class="imagescroller_license">This program is distributed in the hope that it will be useful,'
	    .' but WITHOUT ANY WARRANTY; without even the implied warranty of'
	    .' MERCHAN&shy;TABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the'
	    .' GNU General Public License for more details.</p>'."\n"
	    .'<p class="imagescroller_license">You should have received a copy of the GNU General Public License'
	    .' along with this program.  If not, see'
	    .' <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.</p>'."\n";
}


/**
 * Returns the requirements information view.
 *
 * @return string  The (X)HTML.
 */
function imagescroller_system_check() { // RELEASE-TODO
    global $pth, $tx, $plugin_tx;

    define('IMAGESCROLLER_PHP_VERSION', '4.0.7');
    $ptx = $plugin_tx['imagescroller'];
    $imgdir = $pth['folder']['plugins'].'imagescroller/images/';
    $ok = tag('img src="'.$imgdir.'ok.png" alt="ok"');
    $warn = tag('img src="'.$imgdir.'warn.png" alt="warning"');
    $fail = tag('img src="'.$imgdir.'fail.png" alt="failure"');
    $htm = '<h4>'.$ptx['syscheck_title'].'</h4>'
	    .(version_compare(PHP_VERSION, IMAGESCROLLER_PHP_VERSION) >= 0 ? $ok : $fail)
	    .'&nbsp;&nbsp;'.sprintf($ptx['syscheck_phpversion'], IMAGESCROLLER_PHP_VERSION)
	    .tag('br').tag('br')."\n";
    foreach (array() as $ext) {
	$htm .= (extension_loaded($ext) ? $ok : $fail)
		.'&nbsp;&nbsp;'.sprintf($ptx['syscheck_extension'], $ext).tag('br')."\n";
    }
    $htm .= (!get_magic_quotes_runtime() ? $ok : $fail)
	    .'&nbsp;&nbsp;'.$ptx['syscheck_magic_quotes'].tag('br')."\n";
    $htm .= (strtoupper($tx['meta']['codepage']) == 'UTF-8' ? $ok : $warn)
	    .'&nbsp;&nbsp;'.$ptx['syscheck_encoding'].tag('br').tag('br')."\n";
    $htm .= (file_exists($pth['folder']['plugins'].'jquery/jquery.inc.php') ? $ok : $fail)
	    .'&nbsp;&nbsp;'.$ptx['syscheck_jquery'].tag('br').tag('br')."\n";
    foreach (array('config/', 'css/', 'languages/') as $folder) {
	$folder = $pth['folder']['plugins'].'imagescroller/'.$folder;
	$htm .= (is_writable($folder) ? $ok : $warn)
		.'&nbsp;&nbsp;'.sprintf($ptx['syscheck_writable'], $folder).tag('br')."\n";
    }
    return $htm;
}


/**
 * Handle the plugin administration.
 */
if (!empty($imagescroller)) {
    $o .= print_plugin_admin('off');
    switch ($admin) {
	case '':
	    $o .= imagescroller_version().tag('hr').imagescroller_system_check();
	    break;
	default:
	    $o .= plugin_admin_common($action, $admin, $plugin);
    }
}

?>
