<?php

use Imagescroller\Infra\View;

if (!defined("CMSIMPLE_XH_VERSION")) {header("HTTP/1.1 403 Forbidden"); exit;}

/**
 * @var View $this
 * @var string $onchange
 * @var array<string,string> $options
 * @var string $url
 * @var list<string> $images
 */
?>
<!-- imagescroller admin -->
<select onchange="<?=$onchange?>">
<?foreach ($options as $option => $selected):?>
  <option value="<?=$option?>" <?=$selected?>><?=$option?></option>
<?endforeach?>
</select>
<form action="<?=$url?>" method="post">
  <table>
    <tbody>
<?foreach ($images as $image):?>
      <tr>
        <td>
          <img src="<?=$image?>" width="200" height="" alt="">
          <input type="hidden" name="imagescroller_image[]" value="<?=$image?>">
        </td>
        <td>
          <input type="text" name="imagescroller_title[]">
          <input type="text" name="imagescroller_desc[]">
          <input type="text" name="imagescroller_link[]">
        </td>
      </tr>
<?endforeach?>
    </tbody>
  </table>
  <input type="hidden" name="action" value="save">
  <input type="submit" class="submit">
</form>
