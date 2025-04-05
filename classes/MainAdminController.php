<?php

/*
 * Copyright (c) Christoph M. Becker
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

use Imagescroller\Infra\ImageService;
use Imagescroller\Model\Gallery;
use Plib\CsrfProtector;
use Plib\DocumentStore;
use Plib\Request;
use Plib\Response;
use Plib\View;

class MainAdminController
{
    /** @var CsrfProtector */
    private $csrfProtector;

    /** @var ImageService */
    private $imageService;

    /** @var DocumentStore */
    private $store;

    /** @var View */
    private $view;

    public function __construct(
        CsrfProtector $csrfProtector,
        ImageService $imageService,
        DocumentStore $store,
        View $view
    ) {
        $this->csrfProtector = $csrfProtector;
        $this->imageService = $imageService;
        $this->store = $store;
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
        $galleries = array_map(function ($key) {
            return substr($key, 0, -strlen(".txt"));
        }, $this->store->find('/^[^\/]*\.txt$/'));
        natcasesort($galleries);
        $galleries = array_values($galleries);
        $folders = array_diff($this->imageService->findFolders(), $galleries);
        return $this->respondWith($this->view->render("overview", [
            "galleries" => $galleries,
            "folders" => $folders,
        ]));
    }

    public function edit(Request $request): Response
    {
        $galleryname = $request->get("imagescroller_gallery") ?? "";
        $gallery = $this->store->retrieve($galleryname . ".txt", Gallery::class);
        assert($gallery instanceof Gallery);
        if ($gallery->empty()) {
            $gallery = $this->imageService->galleryFromFolder($galleryname);
        }
        if ($gallery === null) {
            return $this->respondWith($this->view->message("fail", "error_gallery_missing", $galleryname));
        }
        $contents = $gallery->toString();
        return $this->respondWith($this->renderGalleryForm($contents));
    }

    public function save(Request $request): Response
    {
        if (!$this->csrfProtector->check($request->post("imagescroller_token"))) {
            return $this->respondWith($this->view->message("fail", "error_unauthorized"));
        }
        $contents = $request->post("imagescroller_contents") ?? "";
        $galleryname = $request->get("imagescroller_gallery") ?? "";
        $gallery = $this->store->update($galleryname . ".txt", Gallery::class);
        assert($gallery instanceof Gallery);
        $gallery->update($contents);
        if (!$this->store->commit()) {
            return $this->respondWith($this->renderGalleryForm($contents, [["error_save", $galleryname]]));
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
