<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {header("HTTP/1.1 403 Forbidden"); exit;}

/**
 * @var View $this
 * @var string $script
 * @var array<string,mixed> $config
 * @var int $width
 * @var int $height
 * @var int $totalWidth
 * @var list<object{filename:string,url:?string,title:?string,description:?string}> $images
 * @var list<object{class:string,src:string,altkey:string}> $buttons
 * @var list<array{string}> $errors
 */
?>
<!-- imagescroller gallery -->
<script type="module" src="<?=$this->esc($script)?>"></script>
<?foreach ($errors as $error):?>
<p class="xh_warning"><?=$this->text(...$error)?></p>
<?endforeach?>
<div class="imagescroller_container" data-config='<?=$this->json($config)?>'
   style="width: <?=$width?>px; height: <?=$height?>px">
  <div class="imagescroller" style="width: <?=$width?>px; height: <?=$height?>px">
    <div style="width: <?=$totalWidth?>px; height: <?=$height?>px">
<?foreach ($images as $image):?>
      <div class="imagescroller_item">
<?  if ($image->url != ''):?>
        <a href="<?=$this->esc($image->url)?>">
<?  endif?>
          <img src="<?=$this->esc($image->filename)?>" alt="" width="<?=$width?>" height="<?=$height?>">
<?  if ($image->url != ''):?>
        </a>
<?  endif?>
<?  if ($image->title != '' || $image->description != ''):?>
        <div class="imagescroller_info">
          <h6>
<?    if ($image->url != ''):?>
            <a href="<?=$this->esc($image->url)?>">
<?    endif?>
              <?=$this->esc($image->title ?? "")?>
<?    if ($image->url != ''):?>
            </a>
<?    endif?>
          </h6>
          <p><?=$this->esc($image->description ?? "")?></p>
        </div>
<?  endif?>
      </div>
<?endforeach?>
    </div>
  </div>
  <div class="imagescroller_controls" style="width: <?=$width?>px; height: <?=$height?>px;">
<?foreach ($buttons as $button):?>
    <img class="<?=$this->esc($button->class)?>" src="<?=$this->esc($button->src)?>" alt="<?=$this->text($button->altkey)?>">
<?endforeach?>
  </div>
</div>
