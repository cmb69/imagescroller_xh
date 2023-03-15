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
use Imagescroller\Infra\Repository;
use Imagescroller\Infra\View;
use Imagescroller\Value\Image;
use PHPUnit\Framework\TestCase;

class MainControllerTest extends TestCase
{
    public function testRendersGallery(): void
    {
        $conf = XH_includeVar("./config/config.php", "plugin_cf")["imagescroller"];
        $repository = $this->createMock(Repository::class);
        $repository->method("find")->willReturn($this->images());
        $repository->method("dimensionsOf")->willReturn([800, 600, [
            ["error_no_image_new", "./userfiles/images/image.jpg"]]
        ]);
        $view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["imagescroller"]);
        $sut = new MainController("./plugins/imagescroller/", $conf, $repository, $view);
        $response = $sut->defaultAction("test");
        $this->assertEquals("./plugins/imagescroller/", $response->js());
        Approvals::verifyHtml($response->output());
    }

    public function testReportsErrorOnMissingGallery(): void
    {
        $conf = XH_includeVar("./config/config.php", "plugin_cf")["imagescroller"];
        $repository = $this->createMock(Repository::class);
        $view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["imagescroller"]);
        $sut = new MainController("./plugins/imagescroller/", $conf, $repository, $view);
        $response = $sut->defaultAction("missing");
        $this->assertNull($response->js());
        Approvals::verifyHtml($response->output());
    }

    private function images(): array
    {
        return [
            new Image("image1.jpg", "http://example.com/"),
            new Image("image2.jpg", null, "Nice image"),
            new Image("image3.jpg", "/?InternalPage", null, "some image description"),
        ];
    }
}
