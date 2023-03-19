<?

require_once "UserManager.php";

class SessionManager extends Manager {

    private const COOKIE_NAME = "xd4u";
    private const TIME = 60 * 60 * 24 * 30;
    /** @var PDOStatement[] */
    private array $statements;
    private ?User $currentUser = null;
    private ?string $sessionID = null;

    public function __construct(private UserManager $userManager) {

        parent::__construct($this->userManager->getDatabase());

    }

    public function checkSession(): void {

        if(isset($_COOKIE[self::COOKIE_NAME])) {

            /** @var PDOStatement */
            $stmt = &$this->statements['checkSession'];

            try {

                $stmt = $stmt ??
                    $this->database->getConnection()->prepare('SELECT user_id FROM sessions WHERE session_id = :session_id');

                $stmt->execute(["session_id" => $_COOKIE[self::COOKIE_NAME]]);

                if(!$data = $stmt->fetch()) {

                    setcookie(self::COOKIE_NAME, 0, time() - 1);
                    return;

                }

                $this->currentUser = $this->userManager->getUser(new User($data["user_id"], null, null, null));
                $this->sessionID = $_COOKIE[self::COOKIE_NAME];

            }catch(PDOException $e) {

                die($e);

            }

        }

    }

    public function createSession(User $user): bool {

        /** @var PDOStatement */
        $stmt = &$this->statements['createSession'];

        try {

            $stmt = $stmt ??
                $this->database->getConnection()->prepare('INSERT INTO sessions (user_id) VALUES (:user_id) returning session_id');

            $stmt->execute(["user_id" => $user->getId()]);

            if(!$sessionID = $stmt->fetch()) return false;

            setcookie(self::COOKIE_NAME, $sessionID['session_id'], time() + self::TIME);

            return true;

        }catch(PDOException $e) {

            return false;

        }

    }

    public function destroySession(string $sessionID): bool {

        /** @var PDOStatement */
        $stmt = &$this->statements['destroySession'];

        try {

            $stmt = $stmt ??
                $this->database->getConnection()->prepare('DELETE FROM sessions WHERE session_id = :session_id');

            $stmt->execute(["session_id" => $sessionID]);

            setcookie(self::COOKIE_NAME, 0, time() - 1);

            $this->currentUser = null;

            return true;

        }catch(PDOException $e) {

            return false;

        }

    }

    public function getCurrentUser(): ?User { return $this->currentUser; }

    public function getSessionID(): ?string { return $this->sessionID; }

    public function isAuthenticated(): bool { return isset($this->currentUser); }

}