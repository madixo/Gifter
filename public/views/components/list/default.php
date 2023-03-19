<? defined('GIFTER_APP') or die("Don't look, I'm shy! >.<") ?>

<div <?= isset($id) ? "id=\"$id\"" : "" ?> class="list flex--column" <? require __DIR__ . "/../data.php" ?>>
    <div class="list-header">
        <h1 class="list-title"><?= $title ?></h1>
    </div>
    <div class="list-controls flex">
        <? if ($data["selectable"] ?? false) : ?>
            <input type="checkbox" class="list-select" <?= sizeof($items) ? "" : "disabled" ?>>
        <? endif; ?>
        <div class="list-controls-buttons flex">
            <? if ($data["selectable"] ?? false) : ?>
            <button class="list-delete barrel" disabled><i class="fa-regular fa-trash-can"></i>Usuń</button>
            <? endif; ?>
            <? require "buttons/{$templates['buttons']}.php" ?>
        </div>
    </div>
    <div class="list-contents">
        <?
        foreach ($items as $item) {

            $data = [...$data, ...$item['data']];
            $template = $templates['entry'] ?? "default";

            require "entry/$template.php";

        }
        ?>
    </div>
</div>
