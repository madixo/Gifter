<? defined('GIFTER_APP') or die("Don't look, I'm shy! >.<") ?>

<input class="barrel" type="password" <?= isset($name) ? "name=\"$name\"" : "" ?> <?= isset($placeholder) ? "placeholder=\"$placeholder\"" : "" ?> pattern="(?=.*\d)(?=.*[\W_]).{7,}" title="Minimum 7 znaków, przynajmniej jedna duża litera, cyfra i znak specjalny." required>