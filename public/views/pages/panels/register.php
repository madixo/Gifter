<? defined('GIFTER_APP') or die("Don't look, I'm shy! >.<") ?>

<div class="modal flex flex--column flex--center">
    <div class="title">
        <span>Zarejestruj się</span>
    </div>
    [messages message=$message error=$error]
    <form class="modal-form flex flex--column" action="register" method="POST">
        <div class="inputs flex flex--column">
            <input class="barrel" type="email" name="email" placeholder="Email" maxlength="255" <?= isset($email) ? "value=\"$email\"" : ""; ?> required>
            [password name=password placeholder='Hasło']
            [password placeholder='Powtórz hasło']
            <label class="checkbox flex" for="tou">
                <input type="checkbox" name="tou" required>
                <div>Akceptuję <a href="/terms-of-use">regulamin</a></div>
            </label>
        </div>
        <input class="barrel" type="submit" value="Zarejestruj">
    </form>
    <div class="spacer--line"></div>
    <div class="additional-options flex flex--column flex--center">
        <span>Posiadasz konto? <a href="login">Zaloguj się</a></span>
    </div>
</div>
