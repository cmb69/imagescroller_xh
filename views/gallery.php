<div class="imagescroller_container"
     style="width: <?=$width?>px; height: <?=$height?>px">
    <div class="imagescroller" style="width: <?=$width?>px; height: <?=$height?>px">
        <div style="width: <?=$totalWidth?>px; height: <?=$height?>px">
<?php foreach ($gallery->getImages() as $image):?>
            <div class="imagescroller_item">
<?php if ($image->getUrl() != ''):?>
                <a href="<?=$image->getUrl()?>">
<?php endif?>
                    <img src="<?=$image->getFilename()?>" alt="" width="<?=$width?>" height="<?=$height?>">
<?php if ($image->getUrl() != ''):?>
                </a>
<?php endif?>
<?php if ($image->getTitle() != '' || $image->getDescription() != ''):?>
                <div class="imagescroller_info">
                    <h6>
<?php if ($image->getUrl() != ''):?>
                        <a href="<?=$image->getUrl()?>">
<?php endif?>
                            <?=$image->getTitle()?>
<?php if ($image->getUrl() != ''):?>
                        </a>
<?php endif?>
                    </h6>
                    <p><?=$image->getDescription()?></p>
                </div>
<?php endif?>
            </div>
<?php endforeach?>
        </div>
    </div>
    <?=$renderedButtons?>
</div>
