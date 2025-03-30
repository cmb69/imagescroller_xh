<?php

namespace Imagescroller;

use PHPUnit\Framework\TestCase;

class DicTest extends TestCase
{
    public function setUp(): void
    {
        global $pth, $plugin_cf, $plugin_tx;

        $pth = ["folder" => ["content" => "../", "images" => "", "plugins" => ""]];
        $plugin_cf = ["imagescroller" => []];
        $plugin_tx = ["imagescroller" => []];
    }

    public function testMakeMain(): void
    {
        $this->assertInstanceOf(Main::class, Dic::makeMain());
    }

    public function testMakesMainController(): void
    {
        $this->assertInstanceOf(MainController::class, Dic::makeMainController());
    }

    public function testMakesInfoController(): void
    {
        $this->assertInstanceOf(InfoController::class, Dic::makeInfoController());
    }

    public function testMakesMainAdminController(): void
    {
        $this->assertInstanceOf(MainAdminController::class, Dic::makeMainAdminController());
    }
}
