<? defined('GIFTER_APP') or die("Don't look, I'm shy! >.<"); ?>

<button class="list-add barrel"><i class="fa-solid fa-plus"></i>Dodaj <?= $addable['value'] ?></button>
<form class="flex hidden">
    <? require __DIR__ . "/../../input/{$addable['input']}.php" ?>
    <input class="barrel" type="submit" value="Dodaj">
</form>

