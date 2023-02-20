<? defined('GIFTER_APP') or die("Don't look, I'm shy! >.<") ?>

<div class="back">
    <a href="login">
        <i class="fa-solid fa-chevron-left"></i>
    </a>
</div>
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