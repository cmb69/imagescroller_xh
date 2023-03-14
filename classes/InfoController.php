<?php

/*
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

use Imagescroller\View;

class InfoController
{
    /**
     * @return void
     */
    public function defaultAction()
    {
        global $pth, $plugin_tx;

        $view = new View($pth["folder"]["plugins"] . "imagescroller/views/", $plugin_tx["imagescroller"]);
        echo $view->render('info', [
            'logo' => "{$pth['folder']['plugins']}imagescroller/imagescroller.png",
            'version' => Plugin::VERSION,
            'checks' => $this->checks(),
        ]);
    }

    private function checks(): array
    {
        global $pth;

        return [
            $this->checkPhpVersion("5.4.0"),
            $this->checkExtension("json"),
            $this->checkXhVersion("1.6.3"),
            $this->checkPlugin("jquery"),
            $this->checkWritability("{$pth['folder']['plugins']}imagescroller/config/"),
            $this->checkWritability("{$pth['folder']['plugins']}imagescroller/css/"),
            $this->checkWritability("{$pth['folder']['plugins']}imagescroller/languages/")
        ];
    }

    private function checkPhpVersion(string $version)
    {
        global $plugin_tx;

        $state = version_compare(PHP_VERSION, $version, 'ge') ? "success" : "fail";
        return (object) [
            "state" => $state,
            "label" => sprintf($plugin_tx["imagescroller"]['syscheck_phpversion'], $version),
            "stateLabel" => $plugin_tx["imagescroller"]["syscheck_$state"],
        ];
    }

    private function checkExtension(string $name)
    {
        global $plugin_tx;

        $state = extension_loaded($name) ? "success" : "fail";
        return (object) [
            "state" => $state,
            "label" => sprintf($plugin_tx["imagescroller"]['syscheck_extension'], $name),
            "stateLabel" => $plugin_tx["imagescroller"]["syscheck_$state"],
        ];
    }

    private function checkXhVersion(string $version)
    {
        global $plugin_tx;

        $state = version_compare(CMSIMPLE_XH_VERSION, "CMSimple_XH $version", 'ge') ? "success" : "fail";
        return (object) [
            "state" => $state,
            "label" => sprintf($plugin_tx["imagescroller"]['syscheck_xhversion'], $version),
            "stateLabel" => $plugin_tx["imagescroller"]["syscheck_$state"],
        ];
    }

    private function checkPlugin(string $name)
    {
        global $pth, $plugin_tx;

        $state = is_dir($pth["folder"]["plugins"] . $name) ? "success" : "fail";
        return (object) [
            "state" => $state,
            "label" => sprintf($plugin_tx["imagescroller"]['syscheck_plugin'], $name),
            "stateLabel" => $plugin_tx["imagescroller"]["syscheck_$state"],
        ];
    }

    private function checkWritability(string $folder)
    {
        global $plugin_tx;

        $state = is_writable($folder) ? "success" : "warning";
        return (object) [
            "state" => $state,
            "label" => sprintf($plugin_tx["imagescroller"]['syscheck_writable'], $folder),
            "stateLabel" => $plugin_tx["imagescroller"]["syscheck_$state"],
        ];
    }
}
