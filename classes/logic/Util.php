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

namespace Imagescroller\Logic;

use Imagescroller\Value\Image;

class Util
{
    /** @return list<Image> */
    public static function parseRecordJar(string $contents, string $imageFolder): array
    {
        $images = [];
        $records = preg_split('/\R%%\R/', $contents);
        foreach ($records as $record) {
            $lines = array_map("trim", preg_split('/\R/', $record));
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
            $record["image"] = $imageFolder . $record["image"];
            $images[] = new Image(
                $record["image"],
                $record["url"] ?? "",
                $record["title"] ?? "",
                $record["description"] ?? ""
            );
        }
        return $images;
    }

    /** @param list<Image> $images */
    public static function recordJarFromImages(string $gallery, array $images): string
    {
        return implode("\n%%\n", array_map(function (Image $image) use ($gallery) {
            return "Image: " . $gallery . "/" . basename($image->filename());
        }, $images));
    }
}
