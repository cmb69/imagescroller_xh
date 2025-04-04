<?php

namespace Imagescroller\Infra;

use Imagescroller\Model\Gallery;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    public function testFindsAllGalleryFolders(): void
    {
        vfsStream::setup("root", null, ["userfiles" => ["images" => ["one" => [], "two" => [], "three" => []]]]);
        $sut = new Repository("vfs://root/userfiles/images/", "vfs://root/content/imagescroller/");
        $galleries = $sut->findAll();
        $this->assertEquals(["one", "three", "two"], $galleries);
    }

    public function testDimensionsOf(): void
    {
        vfsStream::setup("root", null, ["userfiles" => ["images" => ["test" => [
            "one.jpg" => $this->jpegImage(100, 50),
            "two.jpg" => "blah",
            "three.jpg" => $this->jpegImage(50, 100),
        ]]]]);
        $sut = new Repository("vfs://root/userfiles/images/", "vfs://root/content/imagescroller/");
        $images = $sut->find("test");
        [$width, $height, $errors] = $sut->dimensionsOf($images);
        $this->assertEquals(100, $width);
        $this->assertEquals(50, $height);
        $expected = [
            ["error_no_image_new", "vfs://root/userfiles/images/test/two.jpg"],
            ["error_image_size_new", "vfs://root/userfiles/images/test/three.jpg", 50, 100, 100, 50],
        ];
        $this->assertEquals($expected, $errors);
    }

    private function jpegImage(int $width, int $height): string
    {
        ob_start();
        imagejpeg(imagecreate($width, $height));
        return ob_get_clean();
    }
}
