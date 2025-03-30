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

    public static function fromRecordJar(string $imageFolder, string $contents): self
    {
        $that = new self();
        $that->images = [];
        $records = preg_split('/\R%%\R/', $contents);
        assert($records !== false); // TODO: invalid assertion?
        foreach ($records as $record) {
            $lines = preg_split('/\R/', $record);
            assert($lines !== false); // TODO: invalid assertion?
            $lines = array_map("trim", $lines);
            $record = [];
            foreach ($lines as $line) {
                if ($line !== "") {
                    [$name, $value] = array_map("trim", explode(":", $line, 2));
                    $record[strtolower($name)] = $value;
                }
            }
            if (!isset($record['image'])) {
                continue;
            }
            $record["filename"] = $imageFolder . $record["image"];
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

    public function toRecordJar(string $imageFolder): string
    {
        $res = [];
        foreach ($this->images() as $image) {
            $lines = [];
            $lines[] = "Image: " . substr($image->filename(), strlen($imageFolder));
            if ($image->url()) {
                $lines[] = "URL: " . $image->url();
            }
            if ($image->title()) {
                $lines[] = "Title: " . $image->title();
            }
            if ($image->description()) {
                $lines[] = "Description: " . $image->description();
            }
            $res[] = implode("\n", $lines);
        }
        return implode("\n%%\n", $res);
    }
}
