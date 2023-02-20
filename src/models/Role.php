<?

class Role {

    public function __construct(
        private ?int $id,
        private ?string $name) {}

    public function getId(): ?int {

        return $this->id;

    }

    public function setId(?int $id) {

        $this->id = $id;

    }

    public function getName(): ?string {

        return $this->name;

    }

    public function setName(?string $name) {

        $this->name = $name;

    }

}