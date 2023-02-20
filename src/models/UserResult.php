<?

require_once "User.php";

class UserResult {

    private int $id;
    private string $email;
    private ?string $password = null;
    private ?int $role_id = null;
    private ?string $role_name = null;

    public function toUser() {

        return new User(
                $this->id,
                $this->email,
                $this->password,
                $this->role_id ?
                new Role(
                    $this->role_id,
                    $this->role_name
                ) : null
            );

    }

}