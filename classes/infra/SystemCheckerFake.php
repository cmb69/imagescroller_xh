<?php

/**
 * Copyright 2023 Christoph M. Becker
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

class SystemCheckerFake extends SystemChecker
{
    public function checkVersion(string $actual, string $minimum): bool
    {
        return false;
    }

    public function checkExtension(string $name): bool
    {
        return false;
    }

    public function checkPlugin(string $name): bool
    {
        return false;
    }

    public function checkWritability(string $path): bool
    {
        return false;
    }
}
