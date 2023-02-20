<?

require_once "User.php";
require_once "GiftList.php";

class Gift {

    public function __construct(
        private ?int $id,
        private ?GiftList $giftList,
        private ?string $name,
        private ?string $image,
        private ?float $price,
        private ?string $description,
        private ?User $takenBy) {}

    public function getId(): ?int {

        return $this->id;

    }

    public function setId(?int $id): void {

        $this->id = $id;

    }

    public function getGiftList(): ?GiftList {

        return $this->giftList;

    }

    public function setGiftList(?GiftList $giftList): void {

        $this->giftList = $giftList;

    }

    public function getName(): ?string {

        return $this->name;

    }

    public function setName(?string $name): void {

        $this->name = $name;

    }

    public function getImage(): ?string {

        return $this->image;

    }

    public function setImage(?string $image): void {

        $this->image = $image;

    }

    public function getPrice(): ?float {

        return $this->price;

    }

    public function setPrice(?float $price): void {

        $this->price = $price;

    }

    public function getDescription(): ?string {

        return $this->description;

    }

    public function setDescription(?string $description): void {

        $this->description = $description;

    }

    public function getTakenBy(): ?User {

        return $this->takenBy;

    }

    public function setTakenById(?User $takenBy): void {

        $this->takenBy = $takenBy;

    }

    public function isTaken(): ?bool {

        return $this->takenBy !== null;

    }

}