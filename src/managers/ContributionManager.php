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

        /** @var PDOStatement */
        $stmt = &$this->statements['addContribution'];

        try {

            $stmt = $stmt ??
                $this->database->getConnection()->prepare('INSERT INTO contributions VALUES (:user_id, (SELECT id FROM lists WHERE access_code = :access_code))');

            $stmt->execute([
                'user_id' => $contribution->getUser()->getId(),
                'access_code' => $contribution->getList()->getAccessCode()
            ]);

            if(!$stmt->rowCount()) return null;

            return $this->giftListManager->getList($contribution->getList());

        }catch(PDOException $e) {

            return null;

        }

    }

    public function getContribution(Contribution $contribution): ?GiftList {

        /** @var PDOStatement */
        $stmt = &$this->statements['getContribution'];

        try {

            $stmt = $stmt ??
                $this->database->getConnection()->prepare('SELECT exists(SELECT user_id FROM contributions WHERE user_id = :user_id AND list_id = :list_id)');

            $stmt->execute(['user_id' => $contribution->getUser()->getId(), 'list_id' => $contribution->getList()->getId()]);

            if(!$stmt->fetch()) return null;

            return $this->giftListManager->getList($contribution->getList());

        }catch(PDOException $e) {

            return null;

        }

    }

    public function getUserContributions(User $user): array {

        // if($user->getId() === null) return null;

        /** @var PDOStatement */
        $stmt = &$this->statements['getContributions'];

        try {

            if(!isset($stmt)) {

                $stmt = $this->database->getConnection()->prepare('
                    SELECT * FROM get_contributions
                    WHERE owner_id = :user_id
                ');
                $stmt->setFetchMode(PDO::FETCH_CLASS, 'GiftListResult');

            }

            $stmt->execute(['user_id' => $user->getId()]);

            if(!$contributedLists = $stmt->fetchAll()) return [];

            /** @var GiftListResult[] $contributedLists */
            return array_map(fn(/** @param GiftListResult */ $contributedList) => $contributedList->toGiftList(), $contributedLists);

        }catch(PDOException $e) {

            return [];

        }

    }

    public function deleteContribution(Contribution $contribution): bool {

        // if($list->getId() === null || $list->getOwnerId() === null) return false;

        /** @var PDOStatement */
        $stmt = &$this->statements['deleteContribution'];

        try {

            $stmt = $stmt ??
                $this->database->getConnection()->prepare('DELETE FROM contributions WHERE user_id = :user_id AND list_id = :list_id');

            $stmt->execute(['user_id' => $contribution->getUser()->getId(), 'list_id' => $contribution->getList()->getId()]);

            return $stmt->rowCount();

        }catch(PDOException $e) {

            return false;

        }

    }

    /**
     * @param Contribution[] $contributions
     */
    public function deleteContributions(array $contributions): bool {

        $count = sizeof($contributions);

        if(!$count) return false;

        // foreach($giftLists as $giftList)
        //     if($giftList->getId() === null || $giftList->getOwnerId() === null) return false;

        $listsIds = array_map(fn(/** @param Contribution */ $contribution) => $contribution->getList()->getId(), $contributions);
        $userId = array_unique(array_map(fn(/** @param Contribution */ $contribution) => $contribution->getUser()->getId(), $contributions));

        if(sizeof($userId) !== 1) return false;

        $userId = $userId[0];

        try {

            $stmt = $this->database->getConnection()->
                prepare('DELETE FROM contributions WHERE user_id = ? AND list_id IN ('. implode(', ', array_fill(0, sizeof($listsIds), '?')) .')');

            $stmt->execute([$userId, ...$listsIds]);

            return $stmt->rowCount() == $count;

        }catch(PDOException $e) {

            return false;

        }

    }

}