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

class Gallery
{
    /** @var list<Image> */
    private $images;

    /** @param list<string> $filenames */
    public static function fromFolder(array $filenames): self
    {
        $that = new self();
        $that->images = [];
        foreach ($filenames as $filename) {
            $that->images[] = Image::fromFilename($filename);
        }
        return $that;
    }

    /** @param list<array{filename:string,url:string,title:string,description:string}> $records */
    public static function fromFile(array $records): self
    {
        $that = new self();
        $that->images = [];
        foreach ($records as $record) {
            $that->images[] = Image::fromRecord($record);
        }
        return $that;
    }

    private function __construct()
    {
    }

    /** @return list<Image> */
    public function images(): array
    {
        return $this->images;
    }
}
