<?php

/**
 * The images.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Imagescroller
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2017 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Imagescroller_XH
 */

/**
 * The images.
 *
 * @category CMSimple_XH
 * @package  Imagescroller
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Imagescroller_XH
 */
class Imagescroller_Image
{
    /**
     * The filename.
     *
     * @var string
     */
    protected $filename;

    /**
     * The URL to link to.
     *
     * @var string
     */
    protected $url;

    /**
     * The title.
     *
     * @var string
     */
    protected $title;

    /**
     * The description.
     *
     * @var string
     */
    protected $description;


    /**
     * Creates an image from a filename.
     *
     * @param string $filename A filename.
     *
     * @return Imagescroller_Image
     */
    public static function makeFromFilename($filename)
    {
        $image = new self();
        $image->filename = $filename;
        return $image;
    }

    /**
     * Creates an image from a data record.
     *
     * @param array $record A data record.
     *
     * @return Imagescroller_Image
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

    /**
     * Initializes a new instance.
     */
    public function __construct()
    {
        $this->filename = '';
        $this->url = '';
        $this->title = '';
        $this->description = '';
    }

    /**
     * Returns the filename.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Returns the URL to link to.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Returns the title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns the description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}

?>
