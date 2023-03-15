<?php

use Imagescroller\Infra\View;

/**
 * @var View $this
 * @var string $version
 * @var list<array{class:string,key:string,arg:string,statekey:string}> $checks
 */
?>
<h1>Imagescroller_XH – <?=$version?></h1>
<div>
    <h2><?=$this->text('syscheck_title')?></h2>
<?php foreach ($checks as $check):?>
    <p class="<?=$check['class']?>"><?=$this->text($check['key'], $check['arg'])?><?=$this->text($check['statekey'])?></p>
<?php endforeach?>
</div>
