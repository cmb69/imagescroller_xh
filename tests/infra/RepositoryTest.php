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

    public function testFindsAllGalleries(): void
    {
        vfsStream::setup("root", null, ["content" => ["imagescroller" => [
            "gallery1.txt" => "",
            "gallery2.txt" => "",
            "gallery3.txt" => "",
            "gallery4" => "",
        ]]]);
        $sut = new Repository("vfs://root/userfiles/images/", "vfs://root/content/imagescroller/");
        $galleries = $sut->findAllGalleries();
        $this->assertEquals(["gallery1", "gallery2", "gallery3"], $galleries);
    }

    public function testFindsImagesByFolder(): void
    {
        vfsStream::setup("root", null, ["userfiles" => ["images" => ["test" => [
            "one.jpg" => "",
            "two.jpg" => "",
            "three.jpg" => "",
        ]]]]);
        $sut = new Repository("vfs://root/userfiles/images/", "vfs://root/content/imagescroller/");
        $images = $sut->find("test");
        $expected = Gallery::fromFolder([
            "test/one.jpg",
            "test/two.jpg",
            "test/three.jpg",
        ]);
        $this->assertEquals($expected, $images);
    }

    public function testFindsImagesByFile(): void
    {
        vfsStream::setup("root", null, ["content" => ["imagescroller" => ["gallery.txt" => $this->gallery()]]]);
        $sut = new Repository("vfs://root/userfiles/images/", "vfs://root/content/imagescroller/");
        $images = $sut->find("gallery");
        $expected = Gallery::fromString(<<<'EOS'
            Image: image1.jpg
            URL: http://www.example.com/
            Title: First Photo
            Description: This is the first photo for the image scroller.
            %%
            Image: image37.jpg
            URL: ?A_CMSimple_Page
            %%
            Image: image2.jpg
            URL: ?&mailform
            Title: Contact
            %%
            Image: image3.jpg
            URL: http://3-magi.net/
            Description: My favorite website ;)
            EOS);
        $this->assertEquals($expected, $images);
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

    public function testSavesGallery(): void
    {
        vfsStream::setup("root", null, ["content" => ["imagescroller" => []]]);
        $sut = new Repository("vfs://root/userfiles/images/", "vfs://root/content/imagescroller/");
        $sut->saveGallery("test", $this->gallery());
        $this->assertStringEqualsFile("vfs://root/content/imagescroller/test.txt", $this->gallery());
    }

    private function gallery()
    {
        return <<<EOT
        Image: image1.jpg
        URL: http://www.example.com/
        Title: First Photo
        Description: This is the first photo for the image scroller.
        %%
        Image: image37.jpg
        URL: ?A_CMSimple_Page
        %%
        Image: image2.jpg
        URL: ?&mailform
        Title: Contact
        %%
        Image: image3.jpg
        URL: http://3-magi.net/
        Description: My favorite website ;)
        EOT;
    }

    private function jpegImage(int $width, int $height): string
    {
        ob_start();
        imagejpeg(imagecreate($width, $height));
        return ob_get_clean();
    }
}
