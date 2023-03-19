<?

require_once "GiftList.php";

class GiftListResult {

    private int $list_id;
    private ?int $owner_id = null;
    private ?string $owner_email = null;
    private ?string $owner_password = null;
    private ?int $owner_role_id = null;
    private ?string $owner_role_name = null;
    private string $name;
    private string $access_code;

    public function toGiftList() {

        return new GiftList(
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
                $this->name,
                $this->access_code
            );

    }

}