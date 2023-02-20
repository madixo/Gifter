<?

require_once "Gift.php";
require_once "GiftList.php";

class GiftResult {

        private int $id;
        private ?int $list_id = null;
        private ?int $owner_id = null;
        private ?string $owner_email = null;
        private ?string $owner_password = null;
        private ?int $owner_role_id = null;
        private ?string $owner_role_name = null;
        private ?string $list_name = null;
        private ?string $list_access_code = null;
        private string $name;
        private string $image;
        private ?float $price = null;
        private ?string $description = null;
        private ?int $taken_by_id = null;
        private ?string $taken_by_email = null;
        private ?string $taken_by_password = null;
        private ?int $taken_by_role_id = null;
        private ?string $taken_by_role_name = null;

    public function toGift(): Gift {

        return new Gift(
                $this->id,
                $this->list_id ?
                new GiftList(
                    $this->list_id,
                    $this->owner_id ?
                    new User(
                        $this->owner_id,
                        $this->owner_email,
                        $this->owner_password,
                        $this->owner_role_id ?
                        new Role(
                            $this->owner_role_id,
                            $this->owner_role_name
                        ) : null
                    ) : null,
                    $this->list_name,
                    $this->list_access_code
                ) : null,
                $this->name,
                $this->image,
                $this->price,
                $this->description,
                $this->taken_by_id ?
                new User(
                    $this->taken_by_id,
                    $this->taken_by_email,
                    $this->taken_by_password,
                    $this->taken_by_role_id ?
                    new Role(
                        $this->taken_by_role_id,
                        $this->taken_by_role_name
                    ) : null
                ) : null
            );

    }

}