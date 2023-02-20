<?

require_once "GiftListManager.php";
require_once __DIR__ . "/../models/Contribution.php";

class ContributionManager extends Manager {

    /** @var PDOStatement[] */
    private array $statements;

    public function __construct(private GiftListManager $giftListManager) {

        parent::__construct($this->giftListManager->getDatabase());

    }

    public function addContribution(Contribution $contribution): ?GiftList {

        $this->statements["addContribution"] = $this->statements["addContribution"] ??
            $this->database->getConnection()->prepare("INSERT INTO contributions VALUES (:user_id, (SELECT id FROM gift_lists WHERE access_code = :access_code AND owner_id <> :user_id))");

        try {

            if(!$this->statements["addContribution"]->execute(["user_id" => $contribution->getUser()->getId(), "access_code" => $contribution->getList()->getAccessCode()])) return null;

            return $this->giftListManager->getList($contribution->getList());

        }catch(PDOException $e) {

            return null;

        }

    }

    public function getContribution(Contribution $contribution): ?GiftList {

        $this->statements["getContribution"] = $this->statements["getContribution"] ??
            $this->database->getConnection()->prepare("SELECT exists(SELECT user_id FROM contributions WHERE user_id = :user_id AND list_id = :list_id)");

        if(!$this->statements["getContribution"]->execute(["user_id" => $contribution->getUser()->getId(), "list_id" => $contribution->getList()->getId()])) return null;

        if(!$this->statements["getContribution"]->fetch()) return null;

        return $this->giftListManager->getList($contribution->getList());

    }

    public function getUserContributions(User $user): ?array {

        // if($user->getId() === null) return null;

        if(!isset($this->statements["getContributedLists"])) {

            $this->statements["getContributedLists"] = $this->database->getConnection()->prepare("
                SELECT gift_lists.id, gift_lists.name, users.id owner_id, users.email owner_email,
                    users.password owner_password, roles.id owner_role_id, roles.name owner_role_name,
                    gift_lists.access_code
                FROM contributions
                    JOIN gift_lists ON contributions.list_id = gift_lists.id
                    JOIN users ON gift_lists.owner_id = users.id
                    JOIN roles ON users.role_id = roles.id
                WHERE contributions.user_id = :user_id
            ");
            $this->statements["getContributedLists"]->setFetchMode(PDO::FETCH_CLASS, 'GiftListResult');

        }

        if(!$this->statements["getContributedLists"]->execute(["user_id" => $user->getId()])) return null;

        if(!$lists = $this->statements["getContributedLists"]->fetchAll()) return null;

        return array_map(function($list) {
                return $list->toGiftList();
            }, $lists);

    }

    public function deleteContribution(Contribution $contribution): bool {

        // if($list->getId() === null || $list->getOwnerId() === null) return false;

        $this->statements["deleteContribution"] = $this->statements["deleteContribution"] ??
            $this->database->getConnection()->prepare("DELETE FROM contributions WHERE user_id = :user_id AND list_id = :list_id");

        return $this->statements["deleteContribution"]->execute(["user_id" => $contribution->getUser()->getId(), "list_id" => $contribution->getList()->getId()]);

    }

    /**
     * @param Contribution[] $contributions
     */
    public function deleteContributions(array $contributions): bool {

        if(!sizeof($contributions)) return false;

        // foreach($giftLists as $giftList)
        //     if($giftList->getId() === null || $giftList->getOwnerId() === null) return false;

        $listsIds = array_map(function(/** @var Contribution */ $contribution) { return $contribution->getList()->getId(); }, $contributions);
        $userId = array_unique(array_map(function(/** @var Contribution */ $contribution) { return $contribution->getUser()->getId(); }, $contributions));

        if(sizeof($userId) !== 1) return false;

        $userId = $userId[0];

        return $this->database->getConnection()->
            prepare("DELETE FROM contributions WHERE user_id = ? AND list_id IN (". implode(", ", array_fill(0, sizeof($listsIds), "?")) .")")->
            execute([
                $userId,
                ...$listsIds
            ]);

    }

}