<? defined('GIFTER_APP') or die("Don't look, I'm shy >.<"); ?>

<? if($data["openable"] ?? false): ?>
<a href="list?id=<?= $item["data"]["id"] ?>" class="list-item-open"><i class="fa-solid fa-link"></i>Otwórz</a>
<? endif; ?>
<? if($data["editable"] ?? false): ?>
<a href="edit-list?id=<?= $item["data"]["id"] ?>" class="list-item-edit"><i class="fa-regular fa-pen-to-square"></i>Edytuj</a>
<? endif; ?>
<? if($data["removable"] ?? false): ?>
<button class="list-item-delete"><i class="fa-regular fa-trash-can"></i>Usuń</button>
<? endif; ?>
