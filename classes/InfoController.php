<?php

/*
 * Copyright (c) Christoph M. Becker
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

use Plib\Response;
use Plib\SystemChecker;
use Plib\View;

class InfoController
{
    /** @var string */
    private $pluginFolder;

    /** @var SystemChecker */
    private $systemChecker;

    /** @var View */
    private $view;

    public function __construct(string $pluginFolder, SystemChecker $systemChecker, View $view)
    {
        $this->pluginFolder = $pluginFolder;
        $this->systemChecker = $systemChecker;
        $this->view = $view;
    }

    public function __invoke(): Response
    {
        return Response::create($this->view->render("info", [
            "version" => IMAGESCROLLER_VERSION,
            "checks" => [
                $this->checkPhpVersion("7.1.0"),
                $this->checkXhVersion("1.7.0"),
                $this->checkPlibVersion("1.6"),
                $this->checkPlugin("jquery"),
                $this->checkWritability($this->pluginFolder . "config/"),
                $this->checkWritability($this->pluginFolder . "css/"),
                $this->checkWritability($this->pluginFolder . "languages/")
            ],
        ]))->withTitle($this->view->esc("Imagescroller – 1.0beta3"));
    }

    /** @return object{class:string,key:string,arg:string,statekey:string} */
    private function checkPhpVersion(string $version): object
    {
        $state = $this->systemChecker->checkVersion(PHP_VERSION, $version) ? "success" : "fail";
        return (object) [
            "class" => "xh_$state",
            "key" => "syscheck_phpversion",
            "arg" => $version,
            "statekey" => "syscheck_$state",
        ];
    }

    /** @return object{class:string,key:string,arg:string,statekey:string} */
    private function checkXhVersion(string $version): object
    {
        $state = $this->systemChecker->checkVersion(CMSIMPLE_XH_VERSION, "CMSimple_XH $version") ? "success" : "fail";
        return (object) [
            "class" => "xh_$state",
            "key" => "syscheck_xhversion",
            "arg" => $version,
            "statekey" => "syscheck_$state",
        ];
    }

    /** @return object{class:string,key:string,arg:string,statekey:string} */
    private function checkPlibVersion(string $version): object
    {
        $state = $this->systemChecker->checkPlugin("plib", $version) ? "success" : "fail";
        return (object) [
            "class" => "xh_$state",
            "key" => "syscheck_plibversion",
            "arg" => $version,
            "statekey" => "syscheck_$state",
        ];
    }

    /** @return object{class:string,key:string,arg:string,statekey:string} */
    private function checkPlugin(string $name): object
    {
        $state = $this->systemChecker->checkPlugin($name) ? "success" : "fail";
        return (object) [
            "class" => "xh_$state",
            "key" => "syscheck_plugin",
            "arg" => $name,
            "statekey" => "syscheck_$state",
        ];
    }

    /** @return object{class:string,key:string,arg:string,statekey:string} */
    private function checkWritability(string $folder): object
    {
        $state = $this->systemChecker->checkWritability($folder) ? "success" : "warning";
        return (object) [
            "class" => "xh_$state",
            "key" => "syscheck_writable",
            "arg" => $folder,
            "statekey" => "syscheck_$state",
        ];
    }
}
