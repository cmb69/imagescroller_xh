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

use Imagescroller\Logic\Util;
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

    public function imageFolder(): string
    {
        return $this->imageFolder;
    }

    /** @return list<string> */
    public function findAll(): array
    {
        $galleries = [];
        if (($dir = opendir($this->imageFolder))) {
            while (($filename = readdir($dir)) !== false) {
                if ($filename[0] != '.' && is_dir($this->imageFolder . $filename)) {
                    $galleries[] = $filename;
                }
            }
            closedir($dir);
        }
        natcasesort($galleries);
        return array_values($galleries);
    }

    /** @return list<string> */
    public function findAllGalleries(): array
    {
        $galleries = [];
        if (($dir = opendir($this->contentFolder))) {
            while (($filename = readdir($dir)) !== false) {
                if ($filename[0] != '.' && preg_match('/^(.*)\.txt$/', $filename, $matches)) {
                    $galleries[] = $matches[1];
                }
            }
            closedir($dir);
        }
        natcasesort($galleries);
        return array_values($galleries);
    }

    /** @return list<Image>|null */
    public function find(string $filename): ?array
    {
        if (is_file($this->contentFolder . $filename . ".txt")) {
            return $this->findByFile($this->contentFolder . $filename. ".txt");
        } elseif (is_dir($this->imageFolder . $filename)) {
            return $this->findByFolder($this->imageFolder . $filename);
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
        return Util::parseRecordJar(file_get_contents($filename), $this->imageFolder);
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

    public function saveGallery(string $gallery, string $contents): bool
    {
        return file_put_contents($this->contentFolder . $gallery . ".txt", $contents) !== false;
    }
}
