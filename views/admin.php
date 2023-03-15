<?php

use Imagescroller\Infra\View;
use Imagescroller\Value\Image;

/**
 * @var View $this
 * @var string $onchange
 * @var array<string,string> $options
 * @var string $url
 * @var list<Image> $images
 */
?>
<select onchange="<?=$onchange?>">
<?php foreach ($options as $option => $selected):?>
  <option value="<?=$option?>" <?=$selected?>><?=$option?></option>
<?php endforeach?>
</select>
<form action="<?=$url?>" method="post">
  <table>
    <tbody>
<?php foreach ($images as $img):?>
      <tr>
        <td>
          <img src="<?=$img->filename()?>" width="200" height="" alt="">
          <input type="hidden" name="imagescroller_image[]" value="<?=$img->filename()?>">
        </td>
        <td>
          <input type="text" name="imagescroller_title[]">
          <input type="text" name="imagescroller_desc[]">
          <input type="text" name="imagescroller_link[]">
        </td>
      </tr>
<?php endforeach?>
    </tbody>
  </table>
  <input type="hidden" name="action" value="save">
  <input type="submit" class="submit">
</form>
