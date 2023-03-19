<? defined('GIFTER_APP') or die("Don't look, I'm shy! >.<") ?>

<div class="modal flex flex--column flex--center">
    <div class="title">
        <span>Zaloguj się</span>
    </div>
    [messages message=$message error=$error]
    <form class="modal-form flex flex--column" action="login" method="POST">
        <div class="inputs flex flex--column">
            [email name=email placeholder=Email email=$email]
            [password name=password placeholder='Hasło']
            <a href="forgot-password">Zapomniałeś hasło?</a>
        </div>
        <input class="barrel" type="submit" value="Zaloguj">
    </form>
    <div class="spacer--line"></div>
    <div class="additional-options flex flex--column flex--center">
        <span>Nie masz konta? <a href="/register">Zarejestruj się</a></span>
        <a href="code">Posiadasz kod?</a>
    </div>
</div>
