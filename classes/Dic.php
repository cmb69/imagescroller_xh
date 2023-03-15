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
use Imagescroller\Infra\View;

class Dic
{
    public static function makeRepository(): Repository
    {
        global $pth;

        $contentfolder = $pth['folder']['content'];
        if ($contentfolder[1] === ".") {
            $contentfolder = dirname($contentfolder) . "/";
        }
        $contentfolder = $contentfolder . "imagescroller/";
        return new Repository($pth['folder']['images'], $contentfolder);
    }

    public static function makeView(): View
    {
        global $pth, $plugin_tx;

        return new View($pth["folder"]["plugins"] . "imagescroller/views/", $plugin_tx["imagescroller"]);
    }
}