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

if (!defined("CMSIMPLE_XH_VERSION")) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}

const IMAGESCROLLER_VERSION = "1.0beta3";

/**
 * @param string $path
 * @return string
 */
function imagescroller($path)
{
    return Dic::makeMainController()->defaultAction($path);
}

Dic::makeMain()();
