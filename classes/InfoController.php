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

use Imagescroller\Infra\SystemChecker;
use Imagescroller\Infra\View;
use stdClass;

class InfoController
{
    /** @var string */
    private $pluginFolder;

    /** @var array<string,string> */
    private $text;

    /** @var SystemChecker */
    private $systemChecker;

    /** @var View */
    private $view;

    /** @param array<string,string> $text */
    public function __construct(string $pluginFolder, array $text, SystemChecker $systemChecker, View $view)
    {
        $this->pluginFolder = $pluginFolder;
        $this->text = $text;
        $this->systemChecker = $systemChecker;
        $this->view = $view;
    }

    public function defaultAction(): string
    {
        return $this->view->render('info', [
            'logo' => $this->pluginFolder . "imagescroller.png",
            'version' => Plugin::VERSION,
            'checks' => $this->checks(),
        ]);
    }

    /** @return list<stdClass> */
    private function checks(): array
    {
        return [
            $this->checkPhpVersion("5.4.0"),
            $this->checkExtension("json"),
            $this->checkXhVersion("1.6.3"),
            $this->checkPlugin("jquery"),
            $this->checkWritability($this->pluginFolder . "config/"),
            $this->checkWritability($this->pluginFolder . "css/"),
            $this->checkWritability($this->pluginFolder . "languages/")
        ];
    }

    private function checkPhpVersion(string $version): stdClass
    {
        $state = $this->systemChecker->checkVersion(PHP_VERSION, $version) ? "success" : "fail";
        return (object) [
            "state" => $state,
            "label" => sprintf($this->text['syscheck_phpversion'], $version),
            "stateLabel" => $this->text["syscheck_$state"],
        ];
    }

    private function checkExtension(string $name): stdClass
    {
        $state = $this->systemChecker->checkExtension($name) ? "success" : "fail";
        return (object) [
            "state" => $state,
            "label" => sprintf($this->text['syscheck_extension'], $name),
            "stateLabel" => $this->text["syscheck_$state"],
        ];
    }

    private function checkXhVersion(string $version): stdClass
    {
        $state = $this->systemChecker->checkVersion(CMSIMPLE_XH_VERSION, "CMSimple_XH $version") ? "success" : "fail";
        return (object) [
            "state" => $state,
            "label" => sprintf($this->text['syscheck_xhversion'], $version),
            "stateLabel" => $this->text["syscheck_$state"],
        ];
    }

    private function checkPlugin(string $name): stdClass
    {
        $state = $this->systemChecker->checkPlugin($name) ? "success" : "fail";
        return (object) [
            "state" => $state,
            "label" => sprintf($this->text['syscheck_plugin'], $name),
            "stateLabel" => $this->text["syscheck_$state"],
        ];
    }

    private function checkWritability(string $folder): stdClass
    {
        $state = $this->systemChecker->checkWritability($folder) ? "success" : "warning";
        return (object) [
            "state" => $state,
            "label" => sprintf($this->text['syscheck_writable'], $folder),
            "stateLabel" => $this->text["syscheck_$state"],
        ];
    }
}
