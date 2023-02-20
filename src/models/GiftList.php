<?

require_once "User.php";

class GiftList {

    public function __construct(
        private ?int $id,
        private ?User $owner,
        private ?string $name,
        private ?string $accessCode) {}

    public function getId(): ?int {

        return $this->id;

    }

    public function setId(?int $id): void {

        $this->id = $id;

    }

    public function getOwner(): ?User {

        return $this->owner;

    }

    public function setOwner(?User $owner): void {

        $this->owner = $owner;

    }


    public function getName(): ?string {

        return $this->name;

    }

    public function setName(?string $name) {

        $this->name = $name;

    }


    public function getAccessCode(): ?string {

        return $this->accessCode;

    }

    public function setAccessCode(?string $accessCode): void {

        $this->accessCode = $accessCode;

    }
}