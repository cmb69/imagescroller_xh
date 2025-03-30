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

namespace Imagescroller\Infra;

class Image
{
    /** @var string */
    private $filename;

    /** @var string|null */
    private $url;

    /** @var string|null */
    private $title;

    /** @var string|null */
    private $description;

    public function __construct(
        string $filename,
        ?string $url = null,
        ?string $title = null,
        ?string $description = null
    ) {
        $this->filename = $filename;
        $this->url = $url;
        $this->title = $title;
        $this->description = $description;
    }

    public function filename(): string
    {
        return $this->filename;
    }

    public function url(): ?string
    {
        return $this->url;
    }

    public function title(): ?string
    {
        return $this->title;
    }

    public function description(): ?string
    {
        return $this->description;
    }
}
