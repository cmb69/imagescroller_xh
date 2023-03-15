<?php

/*
 * Copyright 2023 Christoph M. Becker
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

namespace Imagescroller\Infra;

class JavaScript
{
    /** @return void */
    public function emit()
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
