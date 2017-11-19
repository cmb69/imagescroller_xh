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

use Pfw\Url;
use Pfw\View\View;

class MainAdminController
{
    /**
     * @var string
     */
    private $contentFolder;

    public function __construct()
    {
        global $pth, $sl, $cf;

        $contentfolder = $pth['folder']['content'];
        if ($sl !== $cf['language']['default']) {
            $contentfolder = dirname($contentfolder) . '/';
        }
        $this->contentFolder = "{$contentfolder}imagescroller/";
    }

    /**
     * @return void
     */
    public function defaultAction()
    {
        (new View('imagescroller'))
            ->template('gallery_overview')
            ->data([
                'galleries' => $this->findGalleries()
            ])
            ->render();
    }

    /**
     * @return string[]
     */
    private function findGalleries()
    {
        $result = [];
        foreach (scandir($this->contentFolder) as $filename) {
            if (pathinfo($filename, PATHINFO_EXTENSION) === 'txt') {
                $result[] = basename($filename, '.txt');
            }
        }
        return $result;
    }

    /**
     * @return void
     */
    public function newAction()
    {
        (new View('imagescroller'))
            ->template('gallery_editor')
            ->data([
                'isNew' => true,
                'actionurl' => Url::getCurrent()->with('action', 'create'),
                'name' => '',
                'contents' => ''
            ])
            ->render();
    }

    /**
     * @return void
     */
    public function createAction()
    {
        $gallery = $_POST['name'];
        if (XH_writeFile("{$this->contentFolder}$gallery.txt", $_POST['contents'])) {
            $url = Url::getCurrent()->with('action', 'plugin_text')->without('imagescroller_gallery');
            header('Location: ' . $url->getAbsolute(), true, 303);
            exit;
        }
    }

    /**
     * @return void
     */
    public function editAction()
    {
        $gallery = $_GET['imagescroller_gallery'];
        (new View('imagescroller'))
            ->template('gallery_editor')
            ->data([
                'isNew' => false,
                'actionurl' => Url::getCurrent()->with('action', 'update'),
                'name' => $gallery,
                'contents' => XH_readFile("{$this->contentFolder}$gallery.txt")
            ])
            ->render();
        return;
    }

    /**
     * @return void
     */
    public function updateAction()
    {
        $gallery = $_GET['imagescroller_gallery'];
        if (XH_writeFile("{$this->contentFolder}$gallery.txt", $_POST['contents'])) {
            $url = Url::getCurrent()->with('action', 'plugin_text')->without('imagescroller_gallery');
            header('Location: ' . $url->getAbsolute(), true, 303);
            exit;
        }
    }
}
