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

use Imagescroller\Value\Response;

class Responder
{
    public static function respond(Response $response): string
    {
        global $title;

        if ($response->location() !== null) {
            while (ob_get_level()) {
                ob_end_clean();
            }
            header("Location: " . $response->location(), true, 303);
            exit;
        }
        if ($response->title() !== null) {
            $title = $response->title();
        }
        if ($response->js() !== null) {
            self::emitJs($response->js());
        }
        return $response->output();
    }

    /** @return void */
    private static function emitJs(string $pluginFolder)
    {
        global $hjs;
        static $done = false;

        if ($done) {
            return;
        }
        include_once $pluginFolder . "../jquery/jquery.inc.php";
        include_jquery();
        $libraryFolder =  $pluginFolder . "lib/";
        include_jqueryplugin("scrollTo", $libraryFolder . "jquery.scrollTo.min.js");
        include_jqueryplugin("serialScroll", $libraryFolder . "jquery.serialScroll.min.js");
        $filename = $pluginFolder . "imagescroller.min.js";
        if (!file_exists($filename)) {
            $filename = $pluginFolder . "imagescroller.js";
        }
        $hjs .= "<script src=\"$filename\"></script>\n";
        $done = true;
    }
}
