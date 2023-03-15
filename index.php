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

use Imagescroller\Dic;
use Imagescroller\Infra\JavaScript;

/**
 * @param string $path
 * @return string
 */
function imagescroller($path)
{
    global $pth, $plugin_cf;

    $controller = new Imagescroller\MainController(
        $pth["folder"]["plugins"] . "imagescroller/",
        $plugin_cf["imagescroller"],
        Dic::makeRepository(),
        new JavaScript,
        Dic::makeView()
    );
    return $controller->defaultAction($path);
}

(new Imagescroller\Plugin)->run();
