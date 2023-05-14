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
use Imagescroller\Infra\FakeRepository;
use Imagescroller\Infra\FakeRequest;
use Imagescroller\Infra\Image;
use Imagescroller\Infra\Jquery;
use Imagescroller\Infra\View;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class MainControllerTest extends TestCase
{
    private $pluginFolder;
    private $conf;
    private $repository;
    private $jquery;
    private $view;

    public function setUp(): void
    {
        vfsStream::setup("root");
        mkdir("vfs://root/userfiles/images/", 0777, true);
        mkdir("vfs://root/content/imagescroller/", 0777, true);
        touch("vfs://root/userfiles/images/image.jpg");
        $im = imagecreatetruecolor(800, 600);
        imagejpeg($im, "vfs://root/userfiles/images/image1.jpg");
        imagejpeg($im, "vfs://root/userfiles/images/image2.jpg");
        imagejpeg($im, "vfs://root/userfiles/images/image3.jpg");
        $this->pluginFolder = "./plugins/imagescroller/";
        $this->conf = XH_includeVar("./config/config.php", "plugin_cf")["imagescroller"];
        $this->repository = new FakeRepository("vfs://root/userfiles/images/", "vfs://root/content/imagescroller/");
        $this->jquery = $this->createMock(Jquery::class);
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["imagescroller"]);
    }

    private function sut(): MainController
    {
        return new MainController(
            $this->pluginFolder,
            $this->conf,
            $this->repository,
            $this->jquery,
            $this->view
        );
    }

    public function testRendersGallery(): void
    {
        $this->repository->saveGallery("test", $this->repository->recordJarFromImages($this->images(), "vfs://root/userfiles/images/"));
        $request = new FakeRequest(["adm" => true]);
        $response = $this->sut()($request, "test");
        Approvals::verifyHtml($response->output());
    }

    public function testReportsErrorOnMissingGallery(): void
    {
        $request = new FakeRequest();
        $response = $this->sut()($request, "missing");
        Approvals::verifyHtml($response->output());
    }

    private function images(): array
    {
        return [
            new Image("vfs://root/userfiles/images/image.jpg"),
            new Image("vfs://root/userfiles/images/image1.jpg", "http://example.com/"),
            new Image("vfs://root/userfiles/images/image2.jpg", null, "Nice image"),
            new Image("vfs://root/userfiles/images/image3.jpg", "/?InternalPage", null, "some image description"),
        ];
    }
}
