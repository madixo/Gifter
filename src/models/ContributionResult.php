<?

require_once "Contribution.php";

class ContributionResult {

    private int $user_id;
    private string $user_email;
    private ?string $user_password;
    private int $user_role_id;
    private string $user_role_name;
    private int $list_id;
    private int $owner_id;
    private string $owner_email;
    private ?string $owner_password;
    private int $owner_role_id;
    private string $owner_role_name;
    private string $list_name;
    private string $access_code;

    public function toContribution() {

        return new Contribution(
                new User(
                    $this->user_id,
                    $this->user_email,
                    $this->user_password,
                    new Role(
                        $this->user_role_id,
                        $this->user_role_name
                    )
                ),
                new GiftList(
                    $this->list_id,
                    new User(
                        $this->owner_id,
                        $this->owner_email,
                        $this->owner_password,
                        new Role(
                            $this->owner_role_id,
                            $this->owner_role_name
                        )
                    ),
                    $this->list_name,
                    $this->access_code
                )
            );

    }

}