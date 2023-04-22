<?php

use Imagescroller\Infra\View;

if (!defined("CMSIMPLE_XH_VERSION")) {header("HTTP/1.1 403 Forbidden"); exit;}

/**
 * @var View $this
 * @var string $token
 * @var string $contents
 * @var list<array{string}> $errors
 */
?>
<!-- imagescroller admin -->
<section id="imagescroller_admin">
  <h1>Imagescroller – <?=$this->text('menu_main')?></h1>
<?foreach ($errors as $error):?>
  <p class="xh_fail"><?=$this->text(...$error)?></p>
<?endforeach?>
  <form method="post">
    <input type="hidden" name="imagescroller_token" value="<?=$token?>">
    <p>
      <textarea name="imagescroller_contents"><?=$contents?></textarea>
    </p>
    <p>
      <button name="imagescroller_do"><?=$this->text('label_save')?></button>
    </p>
  </form>
</section>
