<?php

/**
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

use Pfw\SystemCheckService;
use Pfw\View\View;

class InfoController
{
    /**
     * @return void
     */
    public function defaultAction()
    {
        global $pth;

        (new View('imagescroller'))
            ->template('info')
            ->data([
                'logo' => "{$pth['folder']['plugins']}imagescroller/imagescroller.png",
                'version' => IMAGESCROLLER_VERSION,
                'checks' => (new SystemCheckService)
                    ->minPhpVersion('5.4.0')
                    ->minXhVersion('1.6.3')
                    ->plugin('pfw')
                    ->plugin('jquery')
                    ->writable("{$pth['folder']['plugins']}imagescroller/config/")
                    ->writable("{$pth['folder']['plugins']}imagescroller/css/")
                    ->writable("{$pth['folder']['plugins']}imagescroller/languages/")
                    ->getChecks()
            ])
            ->render();
    }
}
