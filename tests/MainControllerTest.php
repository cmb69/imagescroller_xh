<?php

namespace Imagescroller;

use ApprovalTests\Approvals;
use Imagescroller\Infra\FakeRepository;
use Imagescroller\Model\Gallery;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Plib\FakeRequest;
use Plib\Jquery;
use Plib\View;

class MainControllerTest extends TestCase
{
    private $pluginFolder;
    private $imageFolder;
    private $conf;
    private $repository;
    private $jquery;
    private $view;

    public function setUp(): void
    {
        vfsStream::setup("root");
        mkdir("vfs://root/userfiles/images/", 0777, true);
        mkdir("vfs://root/content/imagescroller/", 0777, true);
        touch("vfs://root/userfiles/images/image.jpg");
        $im = imagecreatetruecolor(800, 600);
        imagejpeg($im, "vfs://root/userfiles/images/image1.jpg");
        imagejpeg($im, "vfs://root/userfiles/images/image2.jpg");
        imagejpeg($im, "vfs://root/userfiles/images/image3.jpg");
        $this->pluginFolder = "./plugins/imagescroller/";
        $this->imageFolder = "vfs://root/userfiles/images/";
        $this->conf = XH_includeVar("./config/config.php", "plugin_cf")["imagescroller"];
        $this->repository = new FakeRepository("vfs://root/userfiles/images/", "vfs://root/content/imagescroller/");
        $this->jquery = $this->createMock(Jquery::class);
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["imagescroller"]);
    }

    private function sut(): MainController
    {
        return new MainController(
            $this->pluginFolder,
            $this->imageFolder,
            $this->conf,
            $this->repository,
            $this->jquery,
            $this->view
        );
    }

    public function testRendersGallery(): void
    {
        $this->repository->saveGallery("test", $this->gallery()->toString());
        $request = new FakeRequest(["admin" => true]);
        $response = $this->sut()($request, "test");
        Approvals::verifyHtml($response->output());
    }

    public function testReportsErrorOnMissingGallery(): void
    {
        $request = new FakeRequest();
        $response = $this->sut()($request, "missing");
        Approvals::verifyHtml($response->output());
    }

    private function gallery(): Gallery
    {
        return Gallery::fromString(<<<'EOS'
            Image: image.jpg
            %%
            Image: image1.jpg
            URL: http://example.com/
            %%
            Image: image2.jpg
            Title: Nice image
            %%
            Image: image3.jpg
            URL: /?InternalPage
            Description: some image description
            EOS);
    }
}
