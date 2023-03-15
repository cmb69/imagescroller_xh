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

use Imagescroller\Infra\JavaScript;
use Imagescroller\Infra\SystemChecker;
use Imagescroller\Infra\View;

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
            (new JavaScript)->emit();
        }
        if (defined("XH_ADM") && XH_ADM) {
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
        global $admin, $action, $o, $pth, $plugin_tx;

        $o .= print_plugin_admin('off');
        switch ($admin) {
            case '':
                $controller = new InfoController(
                    $pth["folder"]["plugins"] . "imagescroller/",
                    $plugin_tx["imagescroller"],
                    new SystemChecker,
                    new View($pth["folder"]["plugins"] . "imagescroller/views/", $plugin_tx["imagescroller"])
                );
                $o .= $controller->defaultAction();
                break;
            case 'plugin_main':
                $controller = new MainAdminController(Dic::makeRepository(), Dic::makeView());
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
                $o .= plugin_admin_common();
        }
    }
}
