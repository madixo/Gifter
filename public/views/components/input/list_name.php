<? defined('GIFTER_APP') or die("Don't look, I'm shy! >.<"); ?>

<input class="barrel" name="name" type="text" <?= isset($placeholder) ? "placeholder=\"$placeholder\"" : '' ?> pattern=".{1,40}" maxlength="40" required>
