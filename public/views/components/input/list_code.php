<? defined('GIFTER_APP') or die("Don't look, I'm shy! >.<"); ?>

<input class="barrel" name="code" type="text" <?= isset($placeholder) ? "placeholder=\"$placeholder\"" : '' ?> pattern=".{8}" maxlength="8" required>
