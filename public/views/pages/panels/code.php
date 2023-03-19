<? defined('GIFTER_APP') or die("Don't look, I'm shy! >.<") ?>

[back href=login]
<div class="modal flex flex--column flex--center">
    <div class="title">
        <span>Wprowadź kod</span>
    </div>
    [messages message=$message error=$error]
    <form class="modal-form flex flex--column" action="code" method="POST">
        <div class="inputs flex flex--column">
            <input class="barrel" type="text" name="code" placeholder="Kod" maxlength="8" required>
            [email name=email placeholder=Email]
        </div>
        <input class="barrel" type="submit" value="Zatwierdź">
    </form>
</div>