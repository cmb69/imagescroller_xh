<?php

namespace Imagescroller;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\FakeSystemChecker;
use Plib\View;

class InfoControllerTest extends TestCase
{
    private $pluginFolder;
    private $systemChecker;
    private $view;

    public function setUp(): void
    {
        $this->pluginFolder = "./plugins/imagescroller/";
        $this->systemChecker = new FakeSystemChecker();
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["imagescroller"]);
    }

    private function sut(): InfoController
    {
        return new InfoController(
            $this->pluginFolder,
            $this->systemChecker,
            $this->view
        );
    }

    public function testRendersPluginInfo(): void
    {
        $response = $this->sut()();
        $this->assertEquals("Imagescroller – 1.0beta3", $response->title());
        Approvals::verifyHtml($response->output());
    }
}
