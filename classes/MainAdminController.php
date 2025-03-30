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
use Plib\CsrfProtector;
use Plib\Request;
use Plib\Response;
use Plib\View;

class MainAdminController
{
    /** @var CsrfProtector */
    private $csrfProtector;

    /** @var Repository */
    private $repository;

    /** @var View */
    private $view;

    public function __construct(CsrfProtector $csrfProtector, Repository $repository, View $view)
    {
        $this->csrfProtector = $csrfProtector;
        $this->repository = $repository;
        $this->view = $view;
    }

    public function __invoke(Request $request): Response
    {
        switch ($this->action($request)) {
            default:
                return $this->overview();
            case "edit":
                return $this->edit($request);
            case "do_edit":
                return $this->save($request);
        }
    }

    private function action(Request $request): string
    {
        $action = $request->get("action");
        if (!is_string($action)) {
            return "";
        }
        if (!strncmp($action, "do_", strlen("do_"))) {
            return "";
        }
        if ($request->post("imagescroller_do") !== null) {
            $action = "do_" . $action;
        }
        return $action;
    }

    public function overview(): Response
    {
        $galleries = $this->repository->findAllGalleries();
        $folders = array_diff($this->repository->findAll(), $galleries);
        return $this->respondWith($this->view->render("overview", [
            "galleries" => $galleries,
            "folders" => $folders,
        ]));
    }

    public function edit(Request $request): Response
    {
        $gallery = $request->get("imagescroller_gallery") ?? "";
        $images = $this->repository->find($gallery);
        assert($images !== null); // TODO: invalid assertion
        $contents = $this->repository->recordJarFromImages($images, $this->repository->imageFolder());
        return $this->respondWith($this->renderGalleryForm($contents));
    }

    public function save(Request $request): Response
    {
        if (!$this->csrfProtector->check($request->post("imagescroller_token"))) {
            return $this->respondWith($this->view->message("fail", "error_unauthorized"));
        }
        $contents = $request->post("imagescroller_contents") ?? "";
        $gallery = $request->get("imagescroller_gallery") ?? "";
        if (!$this->repository->saveGallery($gallery, $contents)) {
            return $this->respondWith($this->renderGalleryForm($contents, [["error_save", $gallery]]));
        }
        return Response::redirect($request->url()->without("action")->absolute());
    }

    /** @param list<array{string}> $errors */
    private function renderGalleryForm(string $contents, array $errors = []): string
    {
        return $this->view->render("gallery_form", [
            "token" => $this->csrfProtector->token(),
            "contents" => $contents,
            "errors" => $errors,
        ]);
    }

    private function respondWith(string $output): Response
    {
        return Response::create($output)->withTitle("Imagescroller – " . $this->view->text("label_galleries"));
    }
}
