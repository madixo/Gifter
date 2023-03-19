<? defined('GIFTER_APP') or die("Don't look, I'm shy! >.<") ?>

<div class="list-item" <? require __DIR__ . "/../../data.php" ?>>
    <? if($data["selectable"] ?? false): ?>
    <input type="checkbox" class="checkbox list-item-select">
    <? endif; ?>
    <div class="list-item-contents">
        <? foreach($item['contents'] as $text): ?>
        <div class="list-item-text"><?= $text ?></div>
        <? endforeach; ?>
    </div>
    <div class="list-item-controls">
        <?
            $entry_buttons = $templates["entry_buttons"] ?? "default";
            require "buttons/$entry_buttons.php"
        ?>
   </div>
</div>
