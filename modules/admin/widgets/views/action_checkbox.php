<div class="action-checkbox-el action-checkbox-el_<?= $property ?>">
    <div class="action-checkbox-active">
        <input type="hidden" />
        <input type="checkbox" class="filled-in action-checkbox indigo-field" <?= $item[$property] ? "checked=\"checked\"":"" ?> value="1" id="item<?= $property ?><?=$item["id"]?>" />
        <label for="item<?= $property ?><?=$item["id"]?>"></label>
    </div>
    <div class="action-checkbox-text" data-active="Yes" data-nonactive="No">
        <?= $item[$property] ? "Yes":"No" ?>
    </div>
</div>