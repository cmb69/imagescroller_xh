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

use Imagescroller\Infra\JavaScript;
use Imagescroller\Infra\Repository;
use Imagescroller\Infra\View;

class MainController
{
    /** @var string */
    private $pluginFolder;

    /** @var array<string,string> */
    private $conf;

    /** @var Repository */
    private $repository;

    /** @var JavaScript */
    private $javaScript;

    /** @var View */
    private $view;

    /** @param array<string,string> $conf */
    public function __construct(
        string $pluginFolder,
        array $conf,
        Repository $repository,
        JavaScript $javaScript,
        View $view
    ) {
        $this->pluginFolder = $pluginFolder;
        $this->conf = $conf;
        $this->repository = $repository;
        $this->javaScript = $javaScript;
        $this->view = $view;
    }

    public function defaultAction(string $filename): string
    {
        $images = $this->repository->find($filename);
        if ($images === null) {
            return $this->view->error("error_gallery_missing", $filename);
        }
        [$width, $height, $errors] = $this->repository->dimensionsOf($images);
        $this->javaScript->emit();
        $totalWidth = count($images) * $width;
        $config = json_encode([
            'duration' => (int) $this->conf['scroll_duration'],
            'interval' => (int) $this->conf['scroll_interval'],
            'constant' => (bool) $this->conf['rewind_fast'],
            'dynamicControls' => (bool) $this->conf['controls_dynamic']
        ]);
        return $this->view->render("gallery", [
            'images' => $images,
            'width' => $width,
            'height' => $height,
            'totalWidth' => $totalWidth,
            "buttons" => $this->buttonRecords(),
            'config' => $config,
            "errors" => defined("XH_ADM") && XH_ADM ? $errors : [],
        ]);
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
}
