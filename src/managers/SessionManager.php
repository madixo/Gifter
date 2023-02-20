<?

require_once "UserManager.php";

class SessionManager extends Manager {

    private const COOKIE_NAME = "xd4u";
    private const TIME = 60 * 60 * 24 * 30;
    private PDOStatement $createSessionStatement;
    private PDOStatement $destroySessionStatement;
    private PDOStatement $checkSessionStatement;
    private ?User $currentUser = null;
    private ?string $sessionUUID = null;

    public function __construct(private UserManager $userManager) {

        parent::__construct($this->userManager->getDatabase());

    }

    public function checkSession(): void {

        if(isset($_COOKIE[self::COOKIE_NAME])) {

            $this->checkSessionStatement = $this->checkSessionStatement ??
                $this->database->getConnection()->prepare('SELECT user_id FROM sessions WHERE uuid = :uuid');

            try {

                $this->checkSessionStatement->execute(["uuid" => $_COOKIE[self::COOKIE_NAME]]);

                if(!$data = $this->checkSessionStatement->fetch()) {

                    setcookie(self::COOKIE_NAME, 0, time() - 1);
                    return;

                }

                $this->currentUser = $this->userManager->getUser(new User($data["user_id"], null, null, null));
                $this->sessionUUID = $_COOKIE[self::COOKIE_NAME];

            }catch(PDOException $e) {

                print_r($e);

            }

        }

    }

    public function createSession(User $user): bool {

        if($user->getId() === null)
            throw new Exception("Błąd przy tworzeniu sesji.", 1);

        $uuid = Utils::uuidv4();

        $this->createSessionStatement = $this->createSessionStatement ??
            $this->database->getConnection()->prepare('INSERT INTO sessions (user_id, uuid) VALUES (:user_id, :uuid)');

        try {

            $this->createSessionStatement->execute(["user_id" => $user->getId(), "uuid" => $uuid]);

            setcookie(self::COOKIE_NAME, $uuid, time() + self::TIME);

            return true;

        }catch(PDOException $e) {

            return false;

        }

    }

    public function destroySession(string $uuid): bool {

        $this->destroySessionStatement = $this->destroySessionStatement ??
            $this->database->getConnection()->prepare('DELETE FROM sessions WHERE uuid = :uuid');

        try {

            $this->destroySessionStatement->execute(["uuid" => $uuid]);

            setcookie(self::COOKIE_NAME, 0, time() - 1);

            $this->currentUser = null;

            return true;

        }catch(PDOException $e) {

            return false;

        }

    }

    public function getCurrentUser(): ?User { return $this->currentUser; }

    public function getSessionUUID(): ?string { return $this->sessionUUID; }

    public function isAuthenticated(): bool { return isset($this->currentUser); }

}