<?php

/**
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

use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    /**
     * @return void
     */
    public function testMakeFromFilenameSetsFilename()
    {
        $filename = 'foo.bar';
        $image = Image::makeFromFilename($filename);
        $this->assertEquals($filename, $image->getFilename());
    }

    /**
     * @return void
     */
    public function testMakeFromRecordSetsFilename()
    {
        $filename = 'foo.bar';
        $image = Image::makeFromRecord(
            array($filename, null, null, null)
        );
        $this->assertEquals($filename, $image->getFilename());
    }

    /**
     * @return void
     */
    public function testMakeFromRecordSetsUrl()
    {
        $url = 'http://example.com/';
        $image = Image::makeFromRecord(
            array(null, $url, null, null)
        );
        $this->assertEquals($url, $image->getUrl());
    }

    /**
     * @return void
     */
    public function testMakeFromRecordSetsTitle()
    {
        $title = 'foo';
        $image = Image::makeFromRecord(
            array(null, null, $title, null)
        );
        $this->assertEquals($title, $image->getTitle());
    }

    /**
     * @return void
     */
    public function testMakeFromRecordSetsDescription()
    {
        $description = 'foo';
        $image = Image::makeFromRecord(
            array(null, null, null, $description)
        );
        $this->assertEquals($description, $image->getDescription());
    }
}
