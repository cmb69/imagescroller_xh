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

use ApprovalTests\Approvals;
use Imagescroller\Infra\SystemChecker;
use Imagescroller\Infra\View;
use PHPUnit\Framework\TestCase;

class InfoControllerTest extends TestCase
{
    private $pluginFolder;
    private $systemChecker;
    private $view;

    public function setUp(): void
    {
        $this->pluginFolder = "./plugins/imagescroller/";
        $this->systemChecker = $this->createMock(SystemChecker::class);
        $this->systemChecker->method("checkVersion")->willReturn(false);
        $this->systemChecker->method("checkExtension")->willReturn(false);
        $this->systemChecker->method("checkPlugin")->willReturn(false);
        $this->systemChecker->method("checkWritability")->willReturn(false);
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["imagescroller"]);
    }

    private function sut(): InfoController
    {
        return new InfoController(
            $this->pluginFolder,
            $this->systemChecker,
            $this->view
        );
    }

    public function testRendersPluginInfo(): void
    {
        $response = $this->sut()();
        $this->assertEquals("Imagescroller – 1.0beta3", $response->title());
        Approvals::verifyHtml($response->output());
    }
}
