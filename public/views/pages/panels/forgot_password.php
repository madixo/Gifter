<? defined('GIFTER_APP') or die("Don't look, I'm shy! >.<") ?>

[back href=login]
<div class="modal flex flex--column flex--center">
    <div class="title">
        <span>Zresetuj hasło</span>
    </div>
    [messages message=$message error=$error]
    <form class="modal-form flex flex--column" action="forgot-password" method="POST">
        <div class="inputs flex flex--column">
            <input class="barrel" type="text" name="email" placeholder="Email" maxlength="255" required>
        </div>
        <input class="barrel" type="submit" value="Wyślij instrukcje resetowania">
    </form>
</div>