<?php

use Imagescroller\Infra\View;
use Imagescroller\Value\Image;

/**
 * @var View $this
 * @var string $config
 * @var int $width
 * @var int $height
 * @var int $totalWidth
 * @var list<Image> $images
 * @var list<array{class:string,src:string,altkey:string}> $buttons
 * @var list<array{string}> $errors
 */
?>
<?php foreach ($errors as $error):?>
<p class="xh_warning"><?=$this->text(...$error)?></p>
<?php endforeach?>
<div class="imagescroller_container" data-config="<?=$config?>"
     style="width: <?=$width?>px; height: <?=$height?>px">
    <div class="imagescroller" style="width: <?=$width?>px; height: <?=$height?>px">
        <div style="width: <?=$totalWidth?>px; height: <?=$height?>px">
<?php foreach ($images as $image):?>
            <div class="imagescroller_item">
<?php if ($image->url() != ''):?>
                <a href="<?=$image->url()?>">
<?php endif?>
                    <img src="<?=$image->filename()?>" alt="" width="<?=$width?>" height="<?=$height?>">
<?php if ($image->url() != ''):?>
                </a>
<?php endif?>
<?php if ($image->title() != '' || $image->description() != ''):?>
                <div class="imagescroller_info">
                    <h6>
<?php if ($image->url() != ''):?>
                        <a href="<?=$image->url()?>">
<?php endif?>
                            <?=$image->title()?>
<?php if ($image->url() != ''):?>
                        </a>
<?php endif?>
                    </h6>
                    <p><?=$image->description()?></p>
                </div>
<?php endif?>
            </div>
<?php endforeach?>
        </div>
    </div>
    <div class="imagescroller_controls" style="width: <?=$width?>px; height: <?=$height?>px;">
<?php foreach ($buttons as $button):?>
        <img class="<?=$button['class']?>" src="<?=$button['src']?>" alt="<?=$this->text($button['altkey'])?>">
<?php endforeach?>
    </div>
</div>
