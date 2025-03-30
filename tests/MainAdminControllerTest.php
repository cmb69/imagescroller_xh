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
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Plib\CsrfProtector;
use Plib\FakeRequest;
use Plib\View;

class MainAdminControllerTest extends TestCase
{
    private $csrfProtector;
    private $repository;
    private $view;

    public function setUp(): void
    {
        vfsStream::setup("root");
        mkdir("vfs://root/userfiles/images/", 0777, true);
        mkdir("vfs://root/userfiles/images/gallery1", 0777, true);
        mkdir("vfs://root/userfiles/images/gallery2", 0777, true);
        mkdir("vfs://root/userfiles/images/gallery3", 0777, true);
        touch("vfs://root/userfiles/images/image1.jpg");
        touch("vfs://root/userfiles/images/image2.jpg");
        touch("vfs://root/userfiles/images/image3.jpg");
        mkdir("vfs://root/content/imagescroller/", 0777, true);
        $this->csrfProtector = $this->createMock(CsrfProtector::class);
        $this->repository = new FakeRepository("vfs://root/userfiles/images/", "vfs://root/content/imagescroller/");
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["imagescroller"]);
    }

    private function sut(): MainAdminController
    {
        return new MainAdminController(
            $this->csrfProtector,
            $this->repository,
            $this->view
        );
    }

    public function testRendersOverview(): void
    {
        $request = new FakeRequest();
        $response = $this->sut()($request);
        Approvals::verifyHtml($response->output());
    }

    public function testRendersCreateForm(): void
    {
        $request = new FakeRequest(["url" => "http://example.com/?imagescroller&admin=plugin_main&action=edit"]);
        $response = $this->sut()($request);
        $this->assertEquals("Imagescroller – Galleries", $response->title());
        Approvals::verifyHtml($response->output());
    }
    
    public function testReportsCsrfAttack(): void
    {
        $this->csrfProtector->expects($this->once())->method("check")->willReturn(false);
        $request = new FakeRequest([
            "url" => "http://example.com/?imagescroller&admin=plugin_main&action=edit&imagescroller_gallery=gallery2",
            "post" => [
                "imagescroller_contents" => "Image: image1\n%%\nImage: image2\n%%\nImage: image3",
                "imagescroller_do" => "",
            ],
        ]);
        $response = $this->sut()($request);
        $this->assertEquals("Imagescroller – Galleries", $response->title());
        $this->assertStringContainsString("You are not authorized for this action!", $response->output());
    }

    public function testRedirectsAfterSaving(): void
    {
        $this->csrfProtector->expects($this->once())->method("check")->willReturn(true);
        $request = new FakeRequest([
            "url" => "http://example.com/?imagescroller&admin=plugin_main&action=edit&imagescroller_gallery=gallery2",
            "post" => [
                "imagescroller_contents" => "Image: image1\n%%\nImage: image2\n%%\nImage: image3",
                "imagescroller_do" => "",
            ],
        ]);
        $response = $this->sut()($request);
        $this->assertEquals(
            "http://example.com/?imagescroller&admin=plugin_main&imagescroller_gallery=gallery2",
            $response->location()
        );
    }

    public function testReportsFailureToSave(): void
    {
        $this->csrfProtector->expects($this->once())->method("check")->willReturn(true);
        $this->repository->options(["saveGallery" => false]);
        $request = new FakeRequest([
            "url" => "http://example.com/?imagescroller&admin=plugin_main&action=edit&imagescroller_gallery=gallery2",
            "post" => [
                "imagescroller_contents" => "Image: image1\n%%\nImage: image2\n%%\nImage: image3",
                "imagescroller_do" => "",
            ],
        ]);
        $response = $this->sut()($request);
        $this->assertEquals("Imagescroller – Galleries", $response->title());
        Approvals::verifyHtml($response->output());
    }
}
