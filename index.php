<?php

/**
 * Front-End of Imagescroller_XH.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Imagescroller
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2017 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Imagescroller_XH
 */

/*
 * Prevent direct access and usage from unsupported CMSimple_XH versions.
 */
if (!defined('CMSIMPLE_XH_VERSION')
    || strpos(CMSIMPLE_XH_VERSION, 'CMSimple_XH') !== 0
    || version_compare(CMSIMPLE_XH_VERSION, 'CMSimple_XH 1.6', 'lt')
) {
    header('HTTP/1.1 403 Forbidden');
    header('Content-Type: text/plain; charset=UTF-8');
    die(<<<EOT
Imagescroller_XH detected an unsupported CMSimple_XH version.
Uninstall Imagescroller_XH or upgrade to a supported CMSimple_XH version!
EOT
    );
}

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
