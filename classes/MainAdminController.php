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

use Imagescroller\Infra\Repository;
use Imagescroller\Infra\Request;
use Imagescroller\Infra\View;
use Imagescroller\Logic\Util;
use Imagescroller\Value\Image;
use Imagescroller\Value\Response;

class MainAdminController
{
    /** @var Repository */
    private $repository;

    /** @var View */
    private $view;

    public function __construct(Repository $repository, View $view)
    {
        $this->repository = $repository;
        $this->view = $view;
    }

    public function __invoke(Request $request): Response
    {
        switch ($request->action()) {
            default:
                return $this->overview();
            case "create":
                return $this->create();
            case "do_create":
                return $this->doCreate();
        }
    }

    public function overview(): Response
    {
        return Response::create($this->view->render("overview", [
            "folders" => $this->repository->findAll(),
        ]));
    }

    public function create(): Response
    {
        $gallery = $_GET["imagescroller_gallery"] ?? "";
        $images = $this->repository->find($gallery);
        $contents = Util::recordJarFromImages($gallery, $images);
        return Response::create($this->renderGalleryForm($contents));
    }

    public function doCreate(): Response
    {
        $contents = $_POST["imagescroller_contents"];
        $gallery = $_GET["imagescroller_gallery"];
        if (!$this->repository->saveGallery($gallery, $contents)) {
            return Response::create($this->renderGalleryForm($contents, [["error_save", $gallery]]));
        }
        return Response::redirect(CMSIMPLE_URL . "?imagescroller&admin=plugin_main");
    }

    /** @param list<array{string}> $errors */
    private function renderGalleryForm(string $contents, array $errors = []): string
    {
        return $this->view->render("gallery_form", [
            "contents" => $contents,
            "errors" => $errors,
        ]);
    }
}
