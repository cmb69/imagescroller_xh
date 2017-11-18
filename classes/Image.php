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

class Image
{
    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;


    /**
     * @param string $filename
     * @return Image
     */
    public static function makeFromFilename($filename)
    {
        $image = new self();
        $image->filename = $filename;
        return $image;
    }

    /**
     * @param array $record
     * @return Image
     */
    public static function makeFromRecord($record)
    {
        $image = new self();
        $image->filename = $record[0];
        $image->url = isset($record[1]) ? $record[1] : '';
        $image->title = isset($record[2]) ? $record[2] : '';
        $image->description = isset($record[3]) ? $record[3] : '';
        return $image;
    }

    public function __construct()
    {
        $this->filename = '';
        $this->url = '';
        $this->title = '';
        $this->description = '';
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
