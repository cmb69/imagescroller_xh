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

class Gallery
{
    /**
     * @param string $foldername
     * @return Gallery
     */
    public static function makeFromFolder($foldername)
    {
        $gallery = new self();
        $foldername = rtrim($foldername, '/') . '/';
        if (($dir = opendir($foldername)) !== false) {
            while (($filename = readdir($dir)) !== false) {
                $fullFilename = $foldername . $filename;
                if (self::isImageFile($fullFilename)) {
                    $gallery->images[] = Image::makeFromFilename(
                        $fullFilename
                    );
                }
            }
            closedir($dir);
        }
        //natcasesort($imgs); // TODO: add back sorting
        return $gallery;
    }

    /**
     * @param string $filename
     * @return bool
     */
    private static function isImageFile($filename)
    {
        return is_file($filename) && getimagesize($filename);
    }

    /**
     * @param string $filename
     * @return Gallery
     */
    public static function makeFromFile($filename)
    {
        global $pth;

        $gallery = new self();
        $foldername = $pth['folder']['images'];
        $data = file_get_contents($filename);
        $data = str_replace(array("\r\n", "\r"), "\n", $data);
        $records = explode("\n\n", $data);
        foreach ($records as $record) {
            $record = array_map('trim', explode("\n", $record));
            $record[0] = $foldername . $record[0];
            $gallery->images[] = Image::makeFromRecord($record);
        }
        return $gallery;
    }

    /**
     * @var Image[]
     */
    private $images = array();

    /**
     * @return int
     */
    public function getImageCount()
    {
        return count($this->images);
    }

    /**
     * @return Image[]
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @return array
     * @todo Throw exceptions instead of appending to $e.
     */
    public function getDimensions()
    {
        global $e, $plugin_tx;

        $ptx = $plugin_tx['imagescroller'];
        foreach ($this->images as $image) {
            $filename = $image->getFilename();
            if (!is_readable($filename) || !($size = getimagesize($filename))) {
                $e = '<li><strong>' . $ptx['error_no_image'] . '</strong>'
                    . tag('br') . $filename . '</li>';
                continue;
            }
            if (!isset($width)) {
                list($width, $height) = $size;
            } else {
                if (($size[0] != $width || $size[1] != $height) && XH_ADM) {
                    $e .= '<li><strong>'
                        . sprintf($ptx['error_image_size'], $size[0], $size[1], $width, $height)
                        . '</strong>' . tag('br') . $filename . '</li>';
                }
            }
        }
        return array($width, $height);
    }
}
