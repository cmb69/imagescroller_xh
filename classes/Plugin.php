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

class Plugin
{
    const VERSION = '1.0beta3';

    /**
     * @return void
     */
    public function run()
    {
        global $plugin_cf;

        if ($plugin_cf['imagescroller']['autoload']) {
            self::emitJs();
        }
        if (XH_ADM) {
            XH_registerStandardPluginMenuItems(false);
            if (XH_wantsPluginAdministration('imagescroller')) {
                $this->handleAdministration();
            }
        }
    }

    /**
     * @return void
     */
    private function handleAdministration()
    {
        global $admin, $action, $o;

        $o .= print_plugin_admin('off');
        switch ($admin) {
            case '':
                ob_start();
                (new InfoController)->defaultAction();
                $o .= ob_get_clean();
                break;
            case 'plugin_main':
                $controller = new MainAdminController;
                ob_start();
                switch ($action) {
                    case 'edit_gallery':
                        $controller->editAction();
                        break;
                    case 'save':
                        $controller->saveAction();
                        break;
                    default:
                        $controller->defaultAction();
                }
                $o .= ob_get_clean();
                break;
            default:
                $o .= plugin_admin_common($action, $admin, 'imagescroller');
        }
    }

    /**
     * @return void
     */
    public static function emitJs()
    {
        global $pth, $hjs;
        static $again = false;

        if ($again) {
            return;
        }
        $again = true;
        include_once $pth['folder']['plugins'] . 'jquery/jquery.inc.php';
        include_jquery();
        $libraryFolder =  $pth['folder']['plugins'] . 'imagescroller/lib/';
        include_jqueryplugin('scrollTo', $libraryFolder . 'jquery.scrollTo.min.js');
        include_jqueryplugin('serialScroll', $libraryFolder . 'jquery.serialScroll.min.js');
        $filename = "{$pth['folder']['plugins']}imagescroller/imagescroller.min.js";
        if (!file_exists($filename)) {
            $filename = "{$pth['folder']['plugins']}imagescroller/imagescroller.js";
        }
        $hjs .= sprintf('<script type="text/javascript" src="%s"></script>', $filename);
    }
}
