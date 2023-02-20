<? defined('GIFTER_APP') or die("Don't look, I'm shy! >.<") ?>

<? if(isset($error)): ?>
    <div class="message message--error barrel">
        <?= $error ?>
        <div class="close-button"><i class="fa-solid fa-xmark"></i></div>
    </div>
<? endif; ?>
<? if(isset($message)): ?>
    <div class="message barrel">
        <?= $message ?>
        <div class="close-button"><i class="fa-solid fa-xmark"></i></div>
    </div>
<? endif; ?>