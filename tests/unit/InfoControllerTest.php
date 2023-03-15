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
    public function testRendersPluginInfo(): void
    {
        $systemChecker = $this->createMock(SystemChecker::class);
        $systemChecker->method("checkVersion")->willReturn(false);
        $systemChecker->method("checkExtension")->willReturn(false);
        $systemChecker->method("checkPlugin")->willReturn(false);
        $systemChecker->method("checkWritability")->willReturn(false);
        $view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["imagescroller"]);
        $sut = new InfoController("./plugins/imagescroller/", $systemChecker, $view);
        $response = $sut();
        $this->assertEquals("Imagescroller – 1.0beta3", $response->title());
        Approvals::verifyHtml($response->output());
    }
}
