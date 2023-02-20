<?

require_once "User.php";
require_once "GiftList.php";

class Contribution {

    public function __construct(private ?User $user, private ?GiftList $list) {}

    public function getUser(): ?User {

        return $this->user;

    }

    public function setUser(?User $user): void {

        $this->user = $user;

    }

    public function getList(): ?GiftList {

        return $this->list;

    }

    public function setList(?GiftList $list): void {

        $this->list = $list;

    }

}