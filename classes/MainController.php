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

use Plib\Jquery;
use Imagescroller\Infra\Image;
use Imagescroller\Infra\Repository;
use Imagescroller\Infra\Request;
use Imagescroller\Infra\Response;
use Imagescroller\Infra\View;

class MainController
{
    /** @var string */
    private $pluginFolder;

    /** @var array<string,string> */
    private $conf;

    /** @var Repository */
    private $repository;

    /** @var Jquery */
    private $jquery;

    /** @var View */
    private $view;

    /** @param array<string,string> $conf */
    public function __construct(
        string $pluginFolder,
        array $conf,
        Repository $repository,
        Jquery $jquery,
        View $view
    ) {
        $this->pluginFolder = $pluginFolder;
        $this->conf = $conf;
        $this->repository = $repository;
        $this->jquery = $jquery;
        $this->view = $view;
    }

    public function __invoke(Request $request, string $filename): Response
    {
        $images = $this->repository->find($filename);
        if ($images === null) {
            return Response::create($this->view->error("error_gallery_missing", $filename));
        }
        [$width, $height, $errors] = $this->repository->dimensionsOf($images);
        $this->jquery->include();
        $this->jquery->includePlugin("scrollTo", $this->pluginFolder . "lib/jquery.scrollTo.min.js");
        $this->jquery->includePlugin("serialScroll", $this->pluginFolder . "lib/jquery.serialScroll.min.js");
        return Response::create($this->view->render("gallery", [
            "images" => $this->imageRecords($images),
            "width" => $width,
            "height" => $height,
            "totalWidth" => count($images) * $width,
            "buttons" => $this->buttonRecords(),
            "config" => $this->jsConf(),
            "script" => $this->pluginFolder . "imagescroller.min.js",
            "errors" => $request->adm() ? $errors : [],
        ]));
    }

    /**
     * @param list<Image> $images
     * @return list<array{filename:string,url:?string,title:?string,description:?string}>
     */
    private function imageRecords(array $images): array
    {
        return array_map(function (Image $image) {
            return [
                "filename" => $image->filename(),
                "url" => $image->url(),
                "title" => $image->title(),
                "description" => $image->description(),
            ];
        }, $images);
    }

    /** @return list<array{class:string,src:string,altkey:string}> */
    private function buttonRecords(): array
    {
        $records = [];
        foreach (["prev", "stop", "play", "next"] as $button) {
            $class = "imagescroller_$button";
            if (in_array($button, ["prev", "next"], true)) {
                $class .= " imagescroller_prev_next";
            }
            $records[] = [
                "class" => $class,
                "src" => $this->pluginFolder . "images/$button.svg",
                "altkey" => "button_$button",
            ];
        }
        return $records;
    }

    /** @return array{duration:int,interval:int,constant:bool,dynamicControls:bool} */
    private function jsConf(): array
    {
        return [
            "duration" => (int) $this->conf["scroll_duration"],
            "interval" => (int) $this->conf["scroll_interval"],
            "constant" => (bool) $this->conf["rewind_fast"],
            "dynamicControls" => (bool) $this->conf["controls_dynamic"]
        ];
    }
}
