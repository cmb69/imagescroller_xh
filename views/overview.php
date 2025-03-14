<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {header("HTTP/1.1 403 Forbidden"); exit;}

/**
 * @var View $this
 * @var list<string> $galleries
 * @var list<string> $folders
 */
?>
<!-- imagescroller overview -->
<section id="imagescroller_admin">
  <h1>Imagescroller – <?=$this->text('menu_main')?></h1>
  <form method="get">
    <input type="hidden" name="selected" value="imagescroller">
    <input type="hidden" name="admin" value="plugin_main">
    <fieldset>
      <legend><?=$this->text('label_galleries')?></legend>
      <table>
<?foreach ($galleries as $gallery):?>
        <tr>
          <td>
            <label>
              <input type="radio" name="imagescroller_gallery" value="<?=$this->esc($gallery)?>">
              <span><?=$this->esc($gallery)?></span>
            </label>
          </td>
        </tr>
<?endforeach?>
      </table>
    </fieldset>
    <fieldset>
      <legend><?=$this->text('label_folders')?></legend>
      <table>
<?foreach ($folders as $folder):?>
        <tr>
          <td>
            <label>
              <input type="radio" name="imagescroller_gallery" value="<?=$this->esc($folder)?>">
              <span><?=$this->esc($folder)?></span>
            </label>
          </td>
        </tr>
<?endforeach?>
      </table>
    </fieldset>
    <p>
      <button name="action" value="edit"><?=$this->text('label_edit')?></button>
    </p>
  </form>
</section>
