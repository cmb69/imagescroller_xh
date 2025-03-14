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
            return $this->findByFile($this->contentFolder . $filename . ".txt");
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
        $images = [];
        $records = preg_split('/\R%%\R/', (string) file_get_contents($filename));
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
            $record["image"] = $this->imageFolder . $record["image"];
            $images[] = new Image(
                $record["image"],
                $record["url"] ?? "",
                $record["title"] ?? "",
                $record["description"] ?? ""
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
                    $error = ["error_image_size_new", $filename, $size[0], $size[1], $width, $height];
                    /** @var array{string} $error */
                    $errors[] = $error;
                }
            }
        }
        return [$width, $height, $errors]; // @phpstan-ignore-line
    }

    public function saveGallery(string $gallery, string $contents): bool
    {
        return file_put_contents($this->contentFolder . $gallery . ".txt", $contents) !== false;
    }

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
