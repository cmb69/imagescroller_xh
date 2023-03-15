<?php

use Imagescroller\Infra\View;

if (!defined("CMSIMPLE_XH_VERSION")) {header("HTTP/1.1 403 Forbidden"); exit;}

/**
 * @var View $this
 * @var string $config
 * @var int $width
 * @var int $height
 * @var int $totalWidth
 * @var list<array{filename:string,url:?string,title:?string,description:?string}> $images
 * @var list<array{class:string,src:string,altkey:string}> $buttons
 * @var list<array{string}> $errors
 */
?>
<!-- imagescroller gallery -->
<?foreach ($errors as $error):?>
<p class="xh_warning"><?=$this->text(...$error)?></p>
<?endforeach?>
<div class="imagescroller_container" data-config="<?=$config?>"
   style="width: <?=$width?>px; height: <?=$height?>px">
  <div class="imagescroller" style="width: <?=$width?>px; height: <?=$height?>px">
    <div style="width: <?=$totalWidth?>px; height: <?=$height?>px">
<?foreach ($images as $image):?>
      <div class="imagescroller_item">
<?  if ($image['url'] != ''):?>
        <a href="<?=$image['url']?>">
<?  endif?>
          <img src="<?=$image['filename']?>" alt="" width="<?=$width?>" height="<?=$height?>">
<?  if ($image['url'] != ''):?>
        </a>
<?  endif?>
<?  if ($image['title'] != '' || $image['description'] != ''):?>
        <div class="imagescroller_info">
          <h6>
<?    if ($image['url'] != ''):?>
            <a href="<?=$image['url']?>">
<?    endif?>
              <?=$image['title']?>
<?    if ($image['url'] != ''):?>
            </a>
<?    endif?>
          </h6>
          <p><?=$image['description']?></p>
        </div>
<?  endif?>
      </div>
<?endforeach?>
    </div>
  </div>
  <div class="imagescroller_controls" style="width: <?=$width?>px; height: <?=$height?>px;">
<?foreach ($buttons as $button):?>
    <img class="<?=$button['class']?>" src="<?=$button['src']?>" alt="<?=$this->text($button['altkey'])?>">
<?endforeach?>
  </div>
</div>
