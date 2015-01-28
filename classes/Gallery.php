<?php

/**
 * The galleries.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Imagescroller
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2015 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Imagescroller_XH
 */

/**
 * The galleries.
 *
 * @category CMSimple_XH
 * @package  Imagescroller
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Imagescroller_XH
 */
class Imagescroller_Gallery
{
    /**
     * Returns a gallery made from an image folder.
     *
     * @param string $foldername A foldername.
     *
     * @return Imagescroller_Gallery
     */
    public static function makeFromFolder($foldername)
    {
        $gallery = new self();
        $foldername = rtrim($foldername, '/') . '/';
        if (($dir = opendir($foldername)) !== false) {
            while (($filename = readdir($dir)) !== false) {
                $fullFilename = $foldername . $filename;
                if (self::isImageFile($fullFilename)) {
                    $gallery->images[] = Imagescroller_Image::makeFromFilename(
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
     * Returns whether a file is an image file.
     *
     * @param string $filename A filename.
     *
     * @return bool
     */
    protected static function isImageFile($filename)
    {
        return is_file($filename) && getimagesize($filename);
    }

    /**
     * Returns gallery made from an info file.
     *
     * @param string $filename A filename.
     *
     * @return Imagescroller_Gallery
     */
    public static function makeFromFile($filename)
    {
        $gallery = new self();
        $foldername = dirname($filename) . '/';
        $data = file_get_contents($filename);
        $data = str_replace(array("\r\n", "\r"), "\n", $data);
        $records = explode("\n\n", $data);
        foreach ($records as $record) {
            $record = array_map('trim', explode("\n", $record));
            $record[0] = $foldername . $record[0];
            $gallery->images[] = Imagescroller_Image::makeFromRecord($record);
        }
        return $gallery;
    }

    /**
     * The images.
     *
     * @var array<Imagescroller_Image>
     */
    protected $images = array();

    /**
     * Returns the count of the images.
     *
     * @return int
     */
    public function getImageCount()
    {
        return count($this->images);
    }

    /**
     * Returns the images.
     *
     * @return array<Imagescroller_Image>
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Returns the dimensions of the gallery.
     *
     * If the dimensions differ, this will be reported through $e in admin mode.
     *
     * @return array
     *
     * @global string The (X)HTML containing error messages.
     * @global bool   Whether we're in admin mode.
     * @global array  The localization of the plugins.
     *
     * @todo Throw exceptions instead of appending to $e.
     */
    public function getDimensions()
    {
        global $e, $adm, $plugin_tx;

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
                if (($size[0] != $width || $size[1] != $height) && $adm) {
                    $e .= '<li><strong>'
                        . sprintf(
                            $ptx['error_image_size'],
                            $size[0], $size[1], $width, $height
                        )
                        . '</strong>' . tag('br') . $filename . '</li>';
                }
            }
        }
        return array($width, $height);
    }
}

?>
