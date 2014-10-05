<?php

/**
 * Front-End of Imagescroller_XH.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Imagescroller
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2014 Christoph M. Becker <http://3-magi.net>
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
 * The controller.
 */
require_once $pth['folder']['plugin_classes'] . 'Controller.php';

/**
 * The version number.
 */
define('IMAGESCROLLER_VERSION', '@IMAGESCROLLER_VERSION@');

/**
 * The controller.
 *
 * @var Imagescroller_Controller
 */
$_Imagescroller_controller = new Imagescroller_Controller();

/**
 * Returns the imagescroller for the images in $path.
 *
 * @param string $path A directory or info file path.
 *
 * @return string (X)HTML.
 *
 * @global Imagescroller_Controller The plugin controller.
 */
function imagescroller($path)
{
    global $_Imagescroller_controller;

    return $_Imagescroller_controller->main($path);
}

$_Imagescroller_controller->dispatch();

?>
