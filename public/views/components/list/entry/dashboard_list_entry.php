<? defined('GIFTER_APP') or die("Don't look, I'm shy! >.<") ?>

<div class="list-item" data-id="<?= $list["id"] ?>">
    <? if($selectable ?? false): ?>
    <input type="checkbox" class="checkbox list-item-select">
    <? endif; ?>
    <div class="list-item-name"><?= htmlspecialchars($list["name"]) ?> <? if(isset($list["access_code"])): ?>(<?= $list["access_code"]?>)<? endif; ?></div>
    <div class="list-item-controls">
        <? if($openable ?? false): ?>
        <a href="list?id=<?= $list["id"] ?>" class="list-item-open"><i class="fa-solid fa-link"></i>Otwórz</a>
        <? endif; ?>
        <? if($editable ?? false): ?>
        <a href="editList?id=<?= $list["id"] ?>" class="list-item-edit"><i class="fa-regular fa-pen-to-square"></i>Edytuj</a>
        <? endif; ?>
        <? if($removable ?? false): ?>
        <button class="list-item-delete"><i class="fa-regular fa-trash-can"></i>Usuń</button>
        <? endif; ?>
    </div>
</div>