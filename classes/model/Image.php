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

namespace Imagescroller\Model;

class Image
{
    /** @var string */
    private $filename;

    /** @var ?string */
    private $url = null;

    /** @var ?string */
    private $title = null;

    /** @var ?string */
    private $description = null;

    public static function fromFilename(string $filename): self
    {
        $that = new self();
        $that->filename = $filename;
        return $that;
    }

    /** @param array{filename:string,url:string,title:string,description:string} $record */
    public static function fromRecord(array $record): self
    {
        $that = new self();
        $that->filename = $record["filename"];
        $that->url = $record["url"];
        $that->title = $record["title"];
        $that->description = $record["description"];
        return $that;
    }

    private function __construct()
    {
    }

    public function filename(): string
    {
        return $this->filename;
    }

    public function url(): ?string
    {
        return $this->url;
    }

    public function title(): ?string
    {
        return $this->title;
    }

    public function description(): ?string
    {
        return $this->description;
    }
}
