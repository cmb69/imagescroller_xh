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

use Plib\Jquery;

class Main
{
    /** @var string */
    private $pluginFolder;

    /** @var array<string,string> */
    private $conf;

    /** @var Jquery */
    private $jquery;

    /** @param array<string,string> $conf */
    public function __construct(string $pluginFolder, array $conf, Jquery $jquery)
    {
        $this->pluginFolder = $pluginFolder;
        $this->conf = $conf;
        $this->jquery = $jquery;
    }

    /** @return void */
    public function __invoke()
    {
        if ($this->conf["autoload"]) {
            $this->jquery->include();
            $this->jquery->includePlugin("scrollTo", $this->pluginFolder . "lib/jquery.scrollTo.min.js");
            $this->jquery->includePlugin("serialScroll", $this->pluginFolder . "lib/jquery.serialScroll.min.js");
        }
    }
}
