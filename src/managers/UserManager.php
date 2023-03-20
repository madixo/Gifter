<?

require_once 'Manager.php';
require_once __DIR__ . '/../Utils.php';
require_once __DIR__ . '/../models/UserResult.php';

class UserManager extends Manager {

    /** @var PDOStatement[] */
    private array $statements = [];

    public function getUser(User $user): ?User {

        $query = 'SELECT * FROM get_users WHERE';

        try {

            if($user->getId() !== null) {

                /** @var PDOStatement */
                $stmt = &$this->statements['getUserById'];

                if(!isset($stmt)) {

                    $stmt = $this->database->getConnection()->prepare("$query user_id = :data");
                    $stmt->setFetchMode(PDO::FETCH_CLASS, UserResult::class);

                }

                $data = $user->getId();

            }else if($user->getEmail() !== null) {

                /** @var PDOStatement */
                $stmt = &$this->statements['getUserByEmail'];

                if(!isset($stmt)) {

                    $stmt = $this->database->getConnection()->prepare("$query email = :data");
                    $stmt->setFetchMode(PDO::FETCH_CLASS, UserResult::class);

                }

                $data = $user->getEmail();

            }else return null;

            $stmt->execute(['data' => $data]);

            if(!$userResult = $stmt->fetch()) return null;

            return $userResult->toUser();

        }catch(PDOException $e) {

            return null;

        }

    }

    public function insertUser(User $user): array {

        // if($user->getEmail() === null || $user->getPassword() === null || $user->getRole() === null || $user->getRole()->getId() === null) return ['status' => 0, 'message' => 'Błąd krytyczny!'];

        /** @var PDOStatement */
        $stmt = &$this->statements['insertUser'];

        try {

            $stmt = $stmt ??
                $this->database->getConnection()->prepare('INSERT INTO users (email, password) VALUES (:email, :password)');

            $stmt->execute(['email' => $user->getEmail(), 'password' => password_hash($user->getPassword(), PASSWORD_BCRYPT)]);

            return ['status' => true, 'id' => $this->database->getConnection()->lastInsertId()];

        }catch(PDOException $e) {

            if($e->getCode() == 23505)
                return ['status' => false, 'redirect' => '/login', 'message' => 'Użytkownik o podanym emailu istnieje!'];

            return ['status' => false, 'message' => 'Wystąpił błąd podczas rejestracji.'];

        }

    }

    public function updatePassword(string $uuid, User $user): array {

        // if($user->getPassword() === null) return ['status' => 0, 'message' => 'Błąd krytyczny!'];

        /** @var PDOStatement */
        $deleteStmt = &$this->statements['deletePasswordReset'];

        /** @var PDOStatement */
        $updateStmt = &$this->statements['updatePassword'];

        try {

            $deleteStmt = $deleteStmt ??
                $this->database->getConnection()->prepare('DELETE FROM password_resets WHERE user_id = :id');

            $updateStmt = $updateStmt ??
                $this->database->getConnection()->prepare('UPDATE users SET password = :password WHERE user_id = :id');

            $this->database->getConnection()->beginTransaction();

            if(($user_id = $this->requestedPasswordReset($uuid)) == null) {

                $this->database->getConnection()->rollBack();

                return ['status' => false, 'message' => 'Nieprawidłowy link resetu hasła lub link wygasł.'];

            }

            $deleteStmt->execute(['id' => $user_id]);

            $updateStmt->execute(['password' => password_hash($user->getPassword(), PASSWORD_BCRYPT), 'id' => $user_id]);

            $this->database->getConnection()->commit();

            return ['status' => true];

        }catch(PDOException $e) {

            $this->database->getConnection()->rollBack();

            return ['status' => 0, 'message' => 'Wystąpił błąd podczas zmiany hasła.'];

        }

    }

    public function requestedPasswordReset(string $uuid): ?int {

        /** @var PDOStatement */
        $stmt = &$this->statements['getPasswordReset'];

        try {

            $stmt = $stmt ??
                $this->database->getConnection()->prepare('select password_reset_request_exists(:password_reset_id)');

            $stmt->execute(['password_reset_id' => $uuid]);

            if(!$retval = $stmt->fetch()) return null;

            return $retval['password_reset_request_exists'];

        }catch(PDOException $e) {

            return null;

        }

    }

    public function requestPasswordReset(User $user): ?string {

        // if($user->getEmail() === null) return true;

        if(!$user = $this->getUser($user)) return true;

        $stmt = &$this->statements['upsertPasswordReset'];

        try {

            $stmt = $stmt ??
                $this->database->getConnection()->prepare('select request_password_reset(:user_id)');

            $stmt->execute(['user_id' => $user->getId()]);

            if(!$retval = $stmt->fetch()) return null;

            return $retval['request_password_reset'];

        }catch(PDOException $e) {

            return null;

        }

    }

}
