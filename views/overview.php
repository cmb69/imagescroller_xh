<?php

use Imagescroller\Infra\View;

if (!defined("CMSIMPLE_XH_VERSION")) {header("HTTP/1.1 403 Forbidden"); exit;}

/**
 * @var View $this
 * @var list<string> $folders
 */
?>
<!-- imagescroller overview -->
<section id="imagescroller_admin">
  <h1>Imagescroller – <?=$this->text('menu_main')?></h1>
  <form method="get">
    <input type="hidden" name="selected" value="imagescroller">
    <input type="hidden" name="admin" value="plugin_main">
    <table>
<?foreach ($folders as $folder):?>
      <tr>
        <td><input type="radio" name="imagescroller_gallery" value="<?=$folder?>"></td>
        <td><?=$folder?></td>
      </tr>
<?endforeach?>
    </table>
    <p>
      <button name="action" value="create"><?=$this->text('label_create_folder')?></button>
    </p>
  </form>
</section>
