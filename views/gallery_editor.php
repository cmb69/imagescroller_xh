<div>
    <h1>Imagescroller –</h1>
    <form action="<?=$actionurl?>" method="POST">
<?php if ($isNew):?>
        <p>
            <input type="text" name="name" value="<?=$name?>">
        </p>
<?php endif?>
        <p>
            <textarea name="contents" cols="80" rows="25"><?=$contents?></textarea>
        </p>
        <p>
            <button>Save</button>
        </p>
    </form>
</div>
