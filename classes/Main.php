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

class Main
{
    /** @var array<string,string> */
    private $conf;

    /** @var JavaScript */
    private $javaScript;

    /** @param array<string,string> $conf */
    public function __construct(array $conf, JavaScript $javaScript)
    {
        $this->conf = $conf;
        $this->javaScript = $javaScript;
    }

    /** @return void */
    public function __invoke()
    {
        if ($this->conf['autoload']) {
            $this->javaScript->emit();
        }
    }
}
