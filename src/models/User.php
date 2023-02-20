<?

require_once "Role.php";

class User {

    public function __construct(
        private ?int $id,
        private ?string $email,
        private ?string $password,
        private ?Role $role) {}

    public function getId(): ?int {

        return $this->id;

    }

    public function setId(?int $id): void {

        $this->id = $id;

    }

    public function getEmail(): ?string {

        return $this->email;

    }

    public function setEmail(?string $email): void {

        $this->email = $email;

    }

    public function getPassword(): ?string {

        return $this->password;

    }

    public function setPassword(?string $password): void {

        $this->password = $password;

    }


    public function getRole(): ?Role {

        return $this->role;

    }

    public function setRole(?Role $role): void {

        $this->role = $role;

    }

    public function isAnon() {

        return $this->role->getName() === "anon";

    }

}