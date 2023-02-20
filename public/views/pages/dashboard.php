<? defined('GIFTER_APP') or die("Don't look, I'm shy! >.<") ?>

<div id="dashboard" class="flex flex--column">
    <header>
        <div class="logo flex">
            <i class="fa fa-gift"></i>
            <h1 class="title">Gifter</h1>
        </div>
        <div class="name">
            <h1><?= $name ?></h1>
        </div>
        <? if(!$user->isAnon()): ?>
        [profile user=$user]
        <? else: ?>
        <div class="register">
            <a href="register" class="barrel">Zarejestruj się</a>
        </div>
        <? endif; ?>
    </header>
    <main class="flex--column">
        <? require "$panel.php"; ?>
    </main>
</div>