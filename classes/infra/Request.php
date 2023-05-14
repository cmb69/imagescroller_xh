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

namespace Imagescroller\Infra;

class Request
{
    /** @codeCoverageIgnore */
    public static function current(): self
    {
        return new self();
    }

    /** @codeCoverageIgnore */
    public function adm(): bool
    {
        return defined("XH_ADM") && XH_ADM;
    }

    public function url(): Url
    {
        $rest = $this->query();
        if ($rest !== "") {
            $rest = "?" . $rest;
        }
        return Url::from(CMSIMPLE_URL . $rest);
    }

    public function action(): string
    {
        $action = $this->url()->param("action");
        if (!is_string($action)) {
            return "";
        }
        if (!strncmp($action, "do_", strlen("do_"))) {
            return "";
        }
        $post = $this->post();
        if (isset($post["imagescroller_do"])) {
            $action = "do_" . $action;
        }
        return $action;
    }

    public function gallery(): string
    {
        $gallery = $this->url()->param("imagescroller_gallery");
        if (!is_string($gallery)) {
            return "";
        }
        return $gallery;
    }

    /** @return array{contents:string} */
    public function contentsPost(): array
    {
        return [
            "contents" => $this->trimmedPostString("imagescroller_contents"),
        ];
    }

    private function trimmedPostString(string $name): string
    {
        $post = $this->post();
        return isset($post[$name]) && is_string($post[$name]) ? trim($post[$name]) : "";
    }

    /** @codeCoverageIgnore */
    protected function query(): string
    {
        return $_SERVER["QUERY_STRING"];
    }

    /**
     * @return array<string,string|array<string>>
     * @codeCoverageIgnore
     */
    protected function post(): array
    {
        return $_POST;
    }
}
