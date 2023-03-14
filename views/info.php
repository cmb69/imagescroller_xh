<?php

use Imagescroller\View;

/**
 * @var View $this
 * @var string $logo
 * @var string $version
 * @var list<stdClass> $checks
 */
?>
<h1>Imagescroller_XH – Info</h1>
<img src="<?=$logo?>" alt="<?=$this->text('alt_logo')?>">
<p>
    Version: <?=$version?>
</p>
<p>
    Copyright © 2012-2017 <a href="http://3-magi.net">Christoph M. Becker</a>
</p>
<p>
    Imagescroller_XH is powered by <a
    href="https://github.com/flesler/jquery.serialScroll">
    jQuery.serialScroll</a>.
</p>
<p>
    This program is free software: you can redistribute it and/or modify it
    under the terms of the GNU General Public License as published by the Free
    Software Foundation, either version 3 of the License, or (at your option)
    any later version.
</p>
<p>
    This program is distributed in the hope that it will be useful, but
    <em>without any warranty</em>; without even the implied warranty of
    <em>merchantibility</em> or <em>fitness for a particular purpose</em>. See
    the GNU General Public License for more details.
</p>
<p>
    You should have received a copy of the GNU General Public License along with
    this program. If not, see <a
    href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.
</p>
<div>
    <h2><?=$this->text('syscheck_title')?></h2>
<?php foreach ($checks as $check):?>
    <p class="xh_<?=$check->state?>"><?=$this->text('syscheck_message', $check->label, $check->stateLabel)?></p>
<?php endforeach?>
</div>
