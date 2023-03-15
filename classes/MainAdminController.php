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
use Imagescroller\Infra\View;
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

    public function __invoke(string $action): Response
    {
        switch ($action) {
            default:
                return $this->defaultAction();
            case "edit_gallery":
                return $this->editAction();
            case "save":
                return $this->saveAction();
        }
    }

    public function defaultAction(): Response
    {
        return $this->editAction();
    }

    public function editAction(): Response
    {
        global $sn;

        $onchange = "window.document.location.href = '$sn?&imagescroller"
            . "&admin=plugin_main&imagescroller_gallery='+this.value";
        $images = $this->repository->find($_GET["imagescroller_gallery"] ?? "");
        return Response::create($this->view->render("admin", [
            "onchange" => $onchange,
            "options" => $this->options(),
            "url" => "$sn?imagescroller&admin=plugin_main",
            "images" => array_map(function (Image $image) {
                return $image->filename();
            }, $images),
        ]));
    }

    public function saveAction(): Response
    {
        $gallery = array();
        foreach (array_keys($_POST['imagescroller_image']) as $i) {
            $image = array();
            foreach (array('image', 'title', 'desc', 'link') as $key) {
                $image[$key] = $_POST["imagescroller_$key"][$i];
            }
            $gallery[] = $image;
        }
        // var_dump($gallery);
        return Response::create();
    }

    /** @return array<string,string> */
    private function options(): array
    {
        $options = [];
        foreach ($this->repository->findAll() as $gallery) {
            $selected = (isset($_GET['imagescroller_gallery'])
                && $gallery == $_GET['imagescroller_gallery']);
            $options[$gallery] = $selected ? "selected" : "";
        }
        return $options;
    }
}
