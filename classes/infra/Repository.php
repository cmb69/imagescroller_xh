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

namespace Imagescroller\Infra;

use Imagescroller\Value\Image;

class Repository
{
    /** @var string */
    private $imageFolder;

    /** @var string */
    private $contentFolder;

    public function __construct(string $imageFolder, string $contentFolder)
    {
        $this->imageFolder = $imageFolder;
        $this->contentFolder = $contentFolder;
    }

    /** @return list<Image>|null */
    public function find(string $filename): ?array
    {
        if (is_dir($this->imageFolder . $filename)) {
            return $this->findByFolder($this->imageFolder . $filename);
        } elseif (is_file($this->contentFolder . $filename)) {
            return $this->findByFile($this->contentFolder . $filename);
        } else {
            return null;
        }
    }

    /** @return list<Image> */
    private function findByFolder(string $foldername): array
    {
        $foldername = rtrim($foldername, "/") . "/";
        $images = [];
        if (($dir = opendir($foldername)) !== false) {
            while (($filename = readdir($dir)) !== false) {
                $fullFilename = $foldername . $filename;
                if ($this->isImage($fullFilename)) {
                    $images[] = new Image($fullFilename);
                }
            }
            closedir($dir);
        }
        //natcasesort($imgs); // TODO: add back sorting
        return $images;
    }

    private function isImage(string $filename): bool
    {
        $imageexts = ["gif", "jpg", "jpeg", "png", "svg"];
        return is_file($filename)
            && in_array(strtolower(pathinfo($filename, PATHINFO_EXTENSION)), $imageexts);
    }

    /**
     * @param string $filename
     * @return list<Image>
     */
    private function findByFile($filename)
    {
        $images = [];
        $data = file_get_contents($filename);
        $data = str_replace(array("\r\n", "\r"), "\n", $data);
        $records = explode("\n%%\n", $data);
        foreach ($records as $record) {
            $lines = array_map('trim', explode("\n", $record));
            $record = [];
            foreach ($lines as $line) {
                if ($line) {
                    list($name, $value) = array_map('trim', explode(':', $line, 2));
                    $record[$name] = $value;
                }
            }
            $record['Image'] = $this->imageFolder . $record['Image'];
            $images[] = new Image(
                $record['Image'],
                isset($record['URL']) ? $record['URL'] : '',
                isset($record['Title']) ? $record['Title'] : '',
                isset($record['Description']) ? $record['Description'] : ''
            );
        }
        return $images;
    }

    /**
     * @param list<Image> $images
     * @return array{int,int,list<array{string}>}
     */
    public function dimensionsOf(array $images): array
    {
        $width = $height = 0;
        $errors = [];
        foreach ($images as $image) {
            $filename = $image->filename();
            if (!is_readable($filename) || !($size = @getimagesize($filename))) {
                $errors[] = ["error_no_image_new", $filename];
                continue;
            }
            if ($width === 0 && $height === 0) {
                [$width, $height] = $size;
            } else {
                if ($size[0] !== $width || $size[1] !== $height) {
                    $errors[] = ["error_image_size_new", $filename, $size[0], $size[1], $width, $height];
                }
            }
        }
        return [$width, $height, $errors];
    }
}
