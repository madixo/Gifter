<? defined('GIFTER_APP') or die("Don't look, I'm shy! >.<") ?>

<div class="modal flex flex--column flex--center">
    <div class="title">
        <span>Zresetuj hasło</span>
    </div>
    [messages message=$message error=$error]
    <form class="modal-form flex flex--column" action="reset-password" method="POST">
        <div class="inputs flex flex--column">
            [password name=password placeholder=Hasło]
            [password placeholder='Powtórz hasło']
            <input type="hidden" name="uuid" value="<?= $uuid ?>" required>
        </div>
        <input class="barrel" type="submit" value="Zresetuj">
    </form>
</div>