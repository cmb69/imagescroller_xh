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

namespace Imagescroller;

use PHPUnit\Framework\TestCase;
use Plib\Jquery;

class MainTest extends TestCase
{
    private $pluginFolder;
    private $conf;
    private $jquery;

    public function setUp(): void
    {
        $this->pluginFolder = "./plugins/imagescroller/";
        $this->conf = XH_includeVar("./config/config.php", "plugin_cf")["imagescroller"];
        $this->jquery = $this->createMock(Jquery::class);
    }

    private function sut(): Main
    {
        return new Main(
            $this->pluginFolder,
            $this->conf,
            $this->jquery
        );
    }

    public function testDoesNotIncludeJqueryByDefault(): void
    {
        $this->jquery->expects($this->never())->method("include");
        $this->jquery->expects($this->never())->method("includePlugin");
        $this->sut()();
    }

    public function testIncludeJqueryIfConfigured(): void
    {
        $this->conf["autoload"] = "true";
        $this->jquery->expects($this->once())->method("include");
        $this->jquery->expects($this->exactly(2))->method("includePlugin");
        $this->sut()();
    }
}
