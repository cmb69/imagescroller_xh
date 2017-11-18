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
        global $pth, $cf, $sl, $plugin_tx;

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
            return XH_message('fail', $plugin_tx['imagescroller']['error_gallery_missing'], $this->path);
        }
        list($width, $height) = $gallery->getDimensions();
        Controller::emitJs();
        $totalWidth = $gallery->getImageCount() * $width;
        $renderedButtons = new HtmlString($this->renderButtons($width, $height));
        (new View('imagescroller'))
            ->template('gallery')
            ->data(compact('gallery', 'width', 'height', 'totalWidth', 'renderedButtons'))
            ->render();
    }

    /**
     * @param int $width
     * @param int $height
     * @return string
     */
    private function renderButtons($width, $height)
    {
        global $pth, $plugin_tx;

        $html = '';
        foreach (array('prev', 'next', 'play', 'stop') as $btn) {
            $name = $btn;
            $alt = $plugin_tx['imagescroller']['button_' . $btn];
            $img = $pth['folder']['plugins'] . 'imagescroller/images/' . $name
                . '.png';
            list($w, $h) = getimagesize($img);
            $top = 'top:' . intval(($height - $h) / 2) . 'px;';
            $left = ($btn == 'play' || $btn == 'stop')
                ? 'left:' . intval(($width - $w) / 2) . 'px'
                : '';
            $html .= tag(
                'img class="imagescroller_' . $btn . '" src="' . $img
                . '" alt="' . $alt . '"' . ' style="' . $top . $left . '"'
            );
        }
        return $html;
    }
}
