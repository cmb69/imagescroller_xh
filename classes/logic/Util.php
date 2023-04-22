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
    /** @param list<Image> $images */
    public static function recordJarFromImages(array $images, string $imageFolder): string
    {
        return implode("\n%%\n", array_map(function (Image $image) use ($imageFolder) {
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
            return implode("\n", $lines);
        }, $images));
    }
}
