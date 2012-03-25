<?php


/**
 * Handle the plugin administration.
 */
if (!empty($imagescroller)) {
    $o .= print_plugin_admin('off');
    switch ($admin) {
	case '':
	    $o .= 'VERSION';
	    break;
	default:
	    $o .= plugin_admin_common($action, $admin, $plugin);
    }
}

?>
