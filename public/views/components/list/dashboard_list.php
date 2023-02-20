<? defined('GIFTER_APP') or die("Don't look, I'm shy! >.<") ?>

<div <?= isset($id) ? "id=\"$id\"" : "" ?> class="list flex--column" <?= isset($endpoint) ? "data-endpoint=\"$endpoint\"" : "" ?>
<?= $selectable ?? false ? "data-selectable" : "" ?> <?= $openable ?? false ? "data-openable" : "" ?>
<?= $editable ?? false ? "data-editable" : "" ?> <?= $removable ?? false ? "data-removable" : "" ?>>
    <div class="list-header">
        <h1 class="list-title"><?= $title ?></h1>
    </div>
    <div class="list-controls flex">
        <? if($selectable ?? false): ?>
        <input type="checkbox" class="list-select" <?= sizeof($lists) ? "" : "disabled" ?>>
        <? endif; ?>
        <div class="list-controls-buttons flex">
            <? if($selectable ?? false): ?>
            <button class="list-delete barrel" disabled><i class="fa-regular fa-trash-can"></i>Usuń</button>
            <? endif; ?>
            <button class="list-add barrel"><i class="fa-solid fa-plus"></i>Dodaj listę</button>
            <form class="flex hidden">
                <input class="barrel" type="text" pattern=".{1,}" maxlength="40" required>
                <input class="barrel" type="submit" value="Dodaj">
            </form>
        </div>
    </div>
    <div class="list-content">

    </div>
</div>