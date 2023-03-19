<? defined('GIFTER_APP') or die("Don't look, I'm shy! >.<") ?>

<div <?= isset($id) ? "id=\"$id\"" : '' ?> class="input-group flex--column">
    <form>
        <div class="input-group-header flex--space-between">
            <h1 class="input-group-title"><?= $title ?></h1>
            <input class="barrel" type="submit" value="<?= isset($button) ? $button : 'Zapisz' ?>">
        </div>
        <div class="input-group-inputs">
            <? require "group/{$templates['group']}.php" ?>
        </div>
    </form>
</div>
