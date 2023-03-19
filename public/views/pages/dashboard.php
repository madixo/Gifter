<? defined('GIFTER_APP') or die("Don't look, I'm shy! >.<") ?>

<div id="dashboard" class="flex flex--column">
    <header>
        <div class="logo flex">
            <i class="fa fa-gift"></i>
            <h1 class="title">Gifter</h1>
        </div>
        <h1 class="name"><?= $name ?></h1>
        <? if(!$user->isAnon()): ?>
        [profile user=$user]
        <? else: ?>
        <div class="register">
            <a href="register" class="barrel">Zarejestruj się</a>
        </div>
        <? endif; ?>
    </header>
    <main class="flex--column">
        <? /** @var string $panel */ require "panels/$panel.php"; ?>
    </main>
    <div id="notifications"></div>
</div>
