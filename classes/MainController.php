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

use Pfw\View\View;
use Pfw\View\HtmlString;

class MainController
{
    /**
     * @var string
     */
    private $path;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = (string) $path;
    }

    /**
     * @return void
     */
    public function defaultAction()
    {
        global $pth, $cf, $sl, $plugin_cf, $plugin_tx;

        $contentfolder = $pth['folder']['content'];
        if ($sl !== $cf['language']['default']) {
            $contentfolder = dirname($contentfolder) . '/';
        }
        $contentfolder = "{$contentfolder}imagescroller/";
        if (is_dir("{$pth['folder']['images']}{$this->path}")) {
            $gallery = Gallery::makeFromFolder("{$pth['folder']['images']}{$this->path}");
        } elseif (is_file("{$contentfolder}{$this->path}.txt")) {
            $gallery = Gallery::makeFromFile("{$contentfolder}{$this->path}.txt");
        } else {
            echo XH_message('fail', $plugin_tx['imagescroller']['error_gallery_missing'], $this->path);
            return;
        }
        list($width, $height) = $gallery->getDimensions();
        Plugin::emitJs();
        $totalWidth = $gallery->getImageCount() * $width;
        $renderedButtons = new HtmlString($this->renderButtons());
        $config = XH_encodeJson([
            'duration' => (int) $plugin_cf['imagescroller']['scroll_duration'],
            'interval' => (int) $plugin_cf['imagescroller']['scroll_interval'],
            'constant' => (bool) $plugin_cf['imagescroller']['rewind_fast'],
            'dynamicControls' => (bool) $plugin_cf['imagescroller']['controls_dynamic']
        ]);
        (new View('imagescroller'))
            ->template('gallery')
            ->data(compact('gallery', 'width', 'height', 'totalWidth', 'renderedButtons', 'config'))
            ->render();
    }

    /**
     * @return string
     */
    private function renderButtons()
    {
        global $pth, $plugin_tx;

        $html = '';
        foreach (array('prev', 'stop', 'play', 'next') as $btn) {
            $name = $btn;
            $alt = $plugin_tx['imagescroller']['button_' . $btn];
            $img = $pth['folder']['plugins'] . 'imagescroller/images/' . $name
                . '.png';
            $class = 'imagescroller_' . $btn;
            if (in_array($btn, ['prev', 'next'])) {
                $class .= ' imagescroller_prev_next';
            }
            $html .= tag(
                'img class="' . $class . '" src="' . $img
                . '" alt="' . $alt . '"'
            );
        }
        return $html;
    }
}
