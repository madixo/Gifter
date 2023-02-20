<? defined('GIFTER_APP') or die("Don't look, I'm shy! >.<") ?>

<input class="barrel" type="email" <?= isset($name) ? "name=\"$name\"" : "" ?> <?= isset($placeholder) ? "placeholder=\"$placeholder\"" : "" ?> maxlength="255" <?= isset($email) ? "value=\"$email\"" : "" ?> required>