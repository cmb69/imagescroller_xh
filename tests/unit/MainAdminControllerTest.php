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
use Imagescroller\Infra\FakeRequest;
use Imagescroller\Infra\Repository;
use Imagescroller\Infra\View;
use Imagescroller\Value\Image;
use PHPUnit\Framework\TestCase;

class MainAdminControllerTest extends TestCase
{
    public function testRendersOverview(): void
    {
        $sut = $this->sut();
        $request = new FakeRequest(["query" => "imagescroller&admin=plugin_main"]);
        $response = $sut($request);
        Approvals::verifyHtml($response->output());
    }

    public function testRendersCreateForm(): void
    {
        $sut = $this->sut();
        $request = new FakeRequest(["query" => "imagescroller&admin=plugin_main&action=edit"]);
        $response = $sut($request);
        Approvals::verifyHtml($response->output());
    }

    public function testRedirectsAfterSaving(): void
    {
        $sut = $this->sut();
        $request = new FakeRequest([
            "query" => "imagescroller&admin=plugin_main&action=edit&imagescroller_gallery=gallery2",
            "post" => [
                "imagescroller_contents" => "Image: image1\n%%\nImage: image2\n%%\nImage: image3",
                "imagescroller_do" => "",
            ],
        ]);
        $response = $sut($request);
        $this->assertEquals("http://example.com/?imagescroller&admin=plugin_main&imagescroller_gallery=gallery2", $response->location());
    }

    public function testReportsFailureToSave(): void
    {
        $sut = $this->sut(["saveGallery" => false]);
        $request = new FakeRequest([
            "query" => "imagescroller&admin=plugin_main&action=edit&imagescroller_gallery=gallery2",
            "post" => [
                "imagescroller_contents" => "Image: image1\n%%\nImage: image2\n%%\nImage: image3",
                "imagescroller_do" => "",
            ],
        ]);
        $response = $sut($request);
        Approvals::verifyHtml($response->output());
    }

    private function sut(array $opts = [])
    {
        $opts += ["saveGallery" => true];
        $repository = $this->createMock(Repository::class);
        $repository->method("find")->willReturn($this->images());
        $repository->method("findAll")->willReturn(["gallery1", "gallery2", "gallery3"]);
        $repository->method("saveGallery")->willReturn($opts["saveGallery"]);
        $view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["imagescroller"]);
        return new MainAdminController($repository, $view);
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
