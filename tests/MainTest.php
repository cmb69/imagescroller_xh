<?php

namespace Imagescroller;

use PHPUnit\Framework\TestCase;
use Plib\Jquery;

class MainTest extends TestCase
{
    private $pluginFolder;
    private $conf;
    private $jquery;

    public function setUp(): void
    {
        $this->pluginFolder = "./plugins/imagescroller/";
        $this->conf = XH_includeVar("./config/config.php", "plugin_cf")["imagescroller"];
        $this->jquery = $this->createMock(Jquery::class);
    }

    private function sut(): Main
    {
        return new Main(
            $this->pluginFolder,
            $this->conf,
            $this->jquery
        );
    }

    public function testDoesNotIncludeJqueryByDefault(): void
    {
        $this->jquery->expects($this->never())->method("include");
        $this->jquery->expects($this->never())->method("includePlugin");
        $this->sut()();
    }

    public function testIncludeJqueryIfConfigured(): void
    {
        $this->conf["autoload"] = "true";
        $this->jquery->expects($this->once())->method("include");
        $this->jquery->expects($this->exactly(2))->method("includePlugin");
        $this->sut()();
    }
}
