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

    /** @return void */
    public function __invoke(string $action)
    {
        switch ($action) {
            default:
                $this->defaultAction();
                break;
            case "edit_gallery":
                $this->editAction();
                break;
            case "save":
                $this->saveAction();
                break;
        }
    }

    /** @return void */
    public function defaultAction()
    {
        $this->editAction();
    }

    /** @return void */
    public function editAction()
    {
        global $sn;

        $onchange = "window.document.location.href = '$sn?&imagescroller"
            . "&amp;admin=plugin_main&amp;imagescroller_gallery='+this.value";
        $images = $this->repository->find($_GET["imagescroller_gallery"] ?? "");
        echo $this->view->render("admin", [
            "onchange" => $onchange,
            "options" => $this->options(),
            "url" => "$sn?imagescroller&amp;admin=plugin_main",
            "images" => $images,
        ]);
    }

    /** @return void */
    public function saveAction()
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
