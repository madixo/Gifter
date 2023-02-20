<?

require_once "Manager.php";
require_once __DIR__ . "/../Utils.php";
require_once __DIR__ . "/../models/UserResult.php";

class UserManager extends Manager {

    /** @var PDOStatement[] */
    private array $statements = [];

    public function getUser(User $user): ?User {

        $query = "
                SELECT users.id, users.email, users.password, users.role_id, roles.name role_name
                FROM users JOIN roles ON users.role_id = roles.id
                WHERE
            ";

        if($user->getId() !== null) {

            if(!isset($this->statements["getUserById"])) {

                $this->statements["getUserById"] = $this->database->getConnection()->prepare("$query users.id = :data");
                $this->statements["getUserById"]->setFetchMode(PDO::FETCH_CLASS, 'UserResult');

            }

            $stmt = $this->statements["getUserById"];
            $data = $user->getId();

        }else if($user->getEmail() !== null) {

            if(!isset($this->statements["getUserByEmail"])) {

                $this->statements["getUserByEmail"] = $this->database->getConnection()->prepare("$query users.email = :data");
                $this->statements["getUserByEmail"]->setFetchMode(PDO::FETCH_CLASS, 'UserResult');

            }

            $stmt = $this->statements["getUserByEmail"];
            $data = $user->getEmail();

        }else return null;

        if(!$stmt->execute(["data" => $data])) return null;

        if(!$userResult = $stmt->fetch()) return null;

        return $userResult->toUser();

    }

    public function insertUser(User $user): array {

        // if($user->getEmail() === null || $user->getPassword() === null || $user->getRole() === null || $user->getRole()->getId() === null) return ["status" => 0, "message" => "Błąd krytyczny!"];

        $this->statements["insertUser"] = $this->statements["insertUser"] ??
            $this->database->getConnection()->prepare("INSERT INTO users (email, password, role_id) VALUES (:email, :password, :role_id)");

        try {

            return $this->statements["insertUser"]
                ->execute(["email" => $user->getEmail(), "password" => password_hash($user->getPassword(), PASSWORD_BCRYPT), "role_id" => $user->getRole()->getId()]) ?
                    ["status" => true, "id" => $this->database->getConnection()->lastInsertId()] :
                    ["status" => false, "message" => "Wystąpił błąd podczas rejestracji."];

        }catch(PDOException $e) {

            return ["status" => false, "message" => "Użytkownik o podanym emailu istnieje!"];

        }

    }

    public function updatePassword(string $uuid, User $user): array {

        // if($user->getPassword() === null) return ["status" => 0, "message" => "Błąd krytyczny!"];

        $this->statements["getPasswordReset"] = $this->statements["getPasswordReset"] ??
            $this->database->getConnection()->prepare("SELECT users.id FROM users JOIN password_reset ON users.id = password_reset.user_id WHERE password_reset.uuid = :uuid AND timestamp + interval '30 minutes' >= current_timestamp");

        $this->statements["deletePasswordReset"] = $this->statements["deletePasswordReset"] ??
            $this->database->getConnection()->prepare("DELETE FROM password_reset WHERE user_id = :id");

        $this->statements["updatePassword"] = $this->statements["updatePassword"] ??
            $this->database->getConnection()->prepare("UPDATE users SET password = :password WHERE id = :id");

        $this->database->getConnection()->beginTransaction();

        try {

            $this->statements["getPasswordReset"]->execute(["uuid" => $uuid]);

            if(!$result = $this->statements["getPasswordReset"]->fetch()) {

                $this->database->getConnection()->rollBack();

                return ["status" => 0, "message" => "Nieprawidłowy link resetu hasła lub link wygasł."];

            }

            $this->statements["deletePasswordReset"]->execute(["id" => $result["id"]]);

            $this->statements["updatePassword"]->execute(["password" => password_hash($user->getPassword(), PASSWORD_BCRYPT), "id" => $result["id"]]);

            $this->database->getConnection()->commit();

            return ["status" => 1];

        }catch(PDOException $e) {

            $this->database->getConnection()->rollBack();
            print_r($e);

            return ["status" => 0, "message" => "Wystąpił błąd podczas zmiany hasła."];

        }

    }

    public function requestPasswordReset(User $user): bool {

        // if($user->getEmail() === null) return true;

        if(!$user = $this->getUser($user)) return true;

        $this->statements["upsertPasswordReset"] = $this->statements["upsertPasswordReset"] ??
            $this->database->getConnection()->prepare("INSERT INTO password_reset (uuid, user_id) VALUES (:uuid, :id) ON CONFLICT (user_id) DO UPDATE SET uuid = :uuid");

        try {

            return $this->statements["upsertPasswordReset"]->execute(["uuid" => Utils::uuidv4(), "id" => $user->getId()]);

        }catch(PDOException $e) {

            return false;

        }

    }

}