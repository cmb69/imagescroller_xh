<?php

/**
 * Testing the images.
 *
 * PHP version 5
 *
 * @category  Testing
 * @package   Imagescroller
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2017 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Imagescroller_XH
 */

namespace Imagescroller;

use PHPUnit\Framework\TestCase;

/**
 * Testing the images.
 *
 * @category Testing
 * @package  Imagescroller
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Imagescroller_XH
 */
class ImageTest extends TestCase
{
    /**
     * Tests that makeFromFilename() sets the filename.
     *
     * @return void
     */
    public function testMakeFromFilenameSetsFilename()
    {
        $filename = 'foo.bar';
        $image = Image::makeFromFilename($filename);
        $this->assertEquals($filename, $image->getFilename());
    }

    /**
     * Tests that makeFromRecord() sets the filename.
     *
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
     * Tests that makeFromRecord() sets the URL to link to.
     *
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
     * Tests that makeFromRecord() sets the title.
     *
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
     * Tests that makeFromRecord() sets the description.
     *
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
