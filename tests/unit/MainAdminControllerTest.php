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

class MainAdminControllerTest extends TestCase
{
    public function testRendersSelectboxAndGallery(): void
    {
        $_GET = ["imagescroller_gallery" => "gallery2"];
        $repository = $this->createMock(Repository::class);
        $repository->method("find")->willReturn($this->images());
        $repository->method("findAll")->willReturn(["gallery1", "gallery2", "gallery3"]);
        $view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["imagescroller"]);
        $sut = new MainAdminController($repository, $view);
        ob_start();
        $sut->defaultAction();
        $response = ob_get_clean();
        Approvals::verifyHtml($response);
    }

    private function images(): array
    {
        return [
            new Image("image1"),
            new Image("image2"),
            new Image("image3"),
        ];
    }
}
