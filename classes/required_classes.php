<?php

/**
 * The autoloader.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Imagescroller
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2014 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Imagescroller_XH
 */

spl_autoload_register('Imagescroller_autoload');

/**
 * The autoloader.
 *
 * @param string $className A class name.
 *
 * @return void
 *
 * @global array The paths of system files and folders.
 */
function Imagescroller_autoload($className)
{
    global $pth;

    $parts = explode('_', $className);
    if ($parts[0] == 'Imagescroller') {
        include_once $pth['folder']['plugins'] . 'imagescroller/classes/'
            . $parts[1] . '.php';
    }
}

?>
