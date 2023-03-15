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

use Imagescroller\Infra\JavaScript;
use Imagescroller\Infra\Repository;
use Imagescroller\Infra\View;

/**
 * @param string $path
 * @return string
 */
function imagescroller($path)
{
    global $pth, $plugin_cf, $plugin_tx, $sl, $cf;

    $contentfolder = $pth['folder']['content'];
    if ($sl !== $cf['language']['default']) {
        $contentfolder = dirname($contentfolder) . '/';
    }
    $contentfolder = "{$contentfolder}imagescroller/";
    $controller = new Imagescroller\MainController(
        $pth["folder"]["plugins"] . "imagescroller/",
        $plugin_cf["imagescroller"],
        new Repository($pth['folder']['images'], $contentfolder),
        new JavaScript,
        new View($pth["folder"]["plugins"] . "imagescroller/views/", $plugin_tx["imagescroller"])
    );
    return $controller->defaultAction($path);
}

(new Imagescroller\Plugin)->run();
