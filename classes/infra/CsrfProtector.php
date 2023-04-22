<?php

/*
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

/** @codeCoverageIgnore */
class CsrfProtector
{
    public function token(): string
    {
        if (isset($_SESSION["imagescroller_token"])) {
            return $_SESSION["imagescroller_token"];
        }
        $token = base64_encode(random_bytes(15));
        $_SESSION["imagescroller_token"] = $token;
        return $token;
    }

    public function check(): bool
    {
        return isset($_SESSION["imagescroller_token"], $_POST["imagescroller_token"])
            && hash_equals($_SESSION["imagescroller_token"], $_POST["imagescroller_token"]);
    }
}
