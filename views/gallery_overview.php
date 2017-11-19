<div>
    <h1>Imagescroller – <?=$this->text('menu_main')?></h1>
    <form method="GET">
        <input type="hidden" name="selected" value="imagescroller">
        <input type="hidden" name="admin" value="plugin_main">
        <table>
<?php foreach ($galleries as $gallery):?>
            <tr>
                <td><input type="radio" id="imagescroller_gallery_<?=$gallery?>" name="imagescroller_gallery" value="<?=$gallery?>"></td>
                <td><label for="imagescroller_gallery_<?=$gallery?>"><?=$gallery?></label></td>
            </tr>
<?php endforeach?>
        </table>
        <p>
            <button name="action" value="new">Add</button>
            <button name="action" value="edit">Edit</button>
        </p>
    </form>
</div>
