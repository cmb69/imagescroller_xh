<?php

/*
 * Copyright 2023 M. Becker
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

use Imagescroller\Infra\Repository;
use Plib\CsrfProtector;
use Plib\Jquery;
use Plib\SystemChecker;
use Plib\View;

class Dic
{
    public static function makeMain(): Main
    {
        global $pth, $plugin_cf;
        return new Main(
            $pth["folder"]["plugins"] . "imagescroller/",
            $plugin_cf["imagescroller"],
            self::makeJquery()
        );
    }

    public static function makeMainController(): MainController
    {
        global $pth, $plugin_cf;
        return new MainController(
            $pth["folder"]["plugins"] . "imagescroller/",
            $plugin_cf["imagescroller"],
            Dic::makeRepository(),
            self::makeJquery(),
            Dic::makeView()
        );
    }

    public static function makeInfoController(): InfoController
    {
        global $pth;

        return new InfoController(
            $pth["folder"]["plugins"] . "imagescroller/",
            new SystemChecker(),
            self::makeView()
        );
    }

    public static function makeMainAdminController(): MainAdminController
    {
        return new MainAdminController(
            new CsrfProtector(),
            self::makeRepository(),
            self::makeView()
        );
    }

    private static function makeRepository(): Repository
    {
        global $pth;

        $contentfolder = $pth['folder']['content'];
        if ($contentfolder[1] === ".") {
            $contentfolder = dirname($contentfolder) . "/";
        }
        $contentfolder = $contentfolder . "imagescroller/";
        return new Repository($pth['folder']['images'], $contentfolder);
    }

    private static function makeJquery(): Jquery
    {
        global $pth;
        static $instance = null;
        if (!$instance) {
            $instance = new Jquery($pth["folder"]["plugins"] . "jquery/");
        }
        return $instance;
    }

    private static function makeView(): View
    {
        global $pth, $plugin_tx;

        return new View($pth["folder"]["plugins"] . "imagescroller/views/", $plugin_tx["imagescroller"]);
    }
}
