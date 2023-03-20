<? defined('GIFTER_APP') or die("Don't look, I'm shy! >.<") ?>

<div id="profile">
    <button class="profile-content">
        <div class="profile-email"><?= $user->getEmail() ?></div>
        <div class="profile-button">
            <i class="fa-solid fa-chevron-down"></i>
        </div>
    </button>
    <div class="profile-modal">
        <div class="profile-modal-item"><a href="/user">Profil użytkownika</a></div>
        <div class="profile-modal-item"><a href="/settings">Ustawienia</a></div>
        <div class="profile-modal-item"><a href="/logout">Wyloguj się</a></div>
    </div>
</div>