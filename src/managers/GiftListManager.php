<?

require_once __DIR__ . '/../config.php';

require_once 'Manager.php';
require_once __DIR__ . '/../models/GiftListResult.php';

class GiftListManager extends Manager {

    /** @var PDOStatement[] */
    private array $statements = [];
    private string $listsStmt = 'SELECT * FROM get_lists';

    public function insertList(GiftList $list): ?GiftList {

        /** @var PDOStatement */
        $stmt = &$this->statements['insertList'];

        try {

            if(!isset($stmt)) {

                $stmt = $this->database->getConnection()->prepare('
                    SELECT * FROM new_list(:owner_id, :name, ' . LIST_CODE_LENGTH . ', ' . LIST_CODE_MAX_ITER . ');
                ');

                $stmt->setFetchMode(PDO::FETCH_CLASS, 'GiftListResult');

            }

            $stmt->execute([
                'owner_id' => $list->getOwner()->getId(),
                'name' => $list->getName()
            ]);

            if(!$giftListResult = $stmt->fetch()) return null;

            /** @var GiftListResult $giftListResult */
            return $giftListResult->toGiftList();

        }catch(PDOException $e) {

            return null;

        }

    }

//    public function insertList(GiftList $list): ?GiftList {
//
//        // if($list->getName() === null || $list->getOwner() === null || $list->getOwner()->getId() === null) return false;
//
//        if(!isset($this->statements['insertList'])) {
//
//            $this->statements['insertList'] = $this->database->getConnection()->prepare('
//                INSERT INTO lists (owner_id, name) VALUES (:owner_id, :name) RETURNING id, name, access_code
//            ');
//
//            $this->statements['insertList']->setFetchMode(PDO::FETCH_CLASS, 'GiftListResult');
//
//        }
//
//        if(!$this->statements['insertList']->execute(['owner_id' => $list->getOwner()->getId(), 'name' => $list->getName()])) return null;
//
//        if(!$result = $this->statements['insertList']->fetch()) return null;
//
//        return $result->toGiftList();
//
//    }

    public function getList(GiftList $list): ?GiftList {

        if($list->getId() !== null) {

            if($list->getOwner() !== null) return $this->getListAuth($list);

            return $this->getListById($list);

        }

        if($list->getAccessCode() !== null) return $this->getListByAccessCode($list);

        return null;

    }

    public function getListAuth(GiftList $list): ?GiftList {

        // if($list->getId() === null || $list->getOwner() === null || $list->getOwner()->getId() === null) return null;

        /** @var PDOStatement */
        $stmt = &$this->statements['getListById'];

        try {

            if(!isset($stmt)) {

                $stmt = $this->database->getConnection()->prepare("
                    $this->listsStmt
                    WHERE list_id = :id AND owner_id = :owner_id
                ");
                $stmt->setFetchMode(PDO::FETCH_CLASS, 'GiftListResult');

            }

            $stmt->execute(['id' => $list->getId(), 'owner_id' => $list->getOwner()->getId()]);

            if(!$giftListResult = $stmt->fetch()) return null;

            /** @var GiftListResult $giftListResult */
            return $giftListResult->toGiftList();

        }catch(PDOException $e) {

            return null;

        }

    }

    public function getListById(GiftList $list): ?GiftList {

        // if($list->getId() === null || $list->getOwner() === null || $list->getOwner()->getId() === null) return null;

        /** @var PDOStatement */
        $stmt = &$this->statements['getListById'];

        try {

            if(!isset($stmt)) {

                $stmt = $this->database->getConnection()->prepare("
                    $this->listsStmt
                    WHERE list_id = :id
                ");
                $stmt->setFetchMode(PDO::FETCH_CLASS, 'GiftListResult');

            }

            $stmt->execute(['id' => $list->getId()]);

            if(!$giftListResult = $stmt->fetch()) return null;

            /** @var GiftListResult $giftListResult */
            return $giftListResult->toGiftList();

        }catch(PDOException $e) {

            return null;

        }

    }

    public function getListByAccessCode(GiftList $list) {

        /** @var PDOStatement */
        $stmt = &$this->statements['getListByAccessCode'];

        try {

            if(!isset($stmt)) {

                $stmt = $this->database->getConnection()->prepare("
                    $this->listsStmt
                    WHERE access_code = :access_code
                ");
                $stmt->setFetchMode(PDO::FETCH_CLASS, 'GiftListResult');

            }

            $stmt->execute(['access_code' => $list->getAccessCode()]);

            if(!$giftListResult = $stmt->fetch()) return null;

            /** @var GiftListResult $giftListResult */
            return $giftListResult->toGiftList();

        }catch(PDOException $e) {

            return null;

        }

    }

    public function getUserLists(User $user): array {

        // if($user->getId() === null) return null;

        /** @var PDOStatement */
        $stmt = &$this->statements['getUserLists'];

        try {

            if(!isset($stmt)) {

                $stmt = $this->database->getConnection()->prepare("
                    $this->listsStmt
                    WHERE owner_id = :owner_id
                ");
                $stmt->setFetchMode(PDO::FETCH_CLASS, 'GiftListResult');

            }

            $stmt->execute(['owner_id' => $user->getId()]);

            if(!$giftListResults = $stmt->fetchAll()) return [];

            /** @var GiftListResult[] $giftListResults */
            return array_map(fn(/** @var GiftListResult */ $giftListResult) => $giftListResult->toGiftList(), $giftListResults);

        }catch(PDOException $e) {

            return [];

        }

    }

    public function deleteList(GiftList $list): bool {

        // if($list->getId() === null || $list->getOwnerId() === null) return false;

        /** @var PDOStatement */
        $stmt = &$this->statements['deleteList'];

        try {

            $stmt = $stmt ??
                $this->database->getConnection()->prepare('DELETE FROM user_id WHERE list_id = :id AND user_id = :owner_id');

            $stmt->execute(['id' => $list->getId(), 'owner_id' => $list->getOwner()->getId()]);

            return $stmt->rowCount();

        }catch(PDOException $e) {

            return false;

        }

    }

    /**
     * @param GiftList[] $giftLists
     */
    public function deleteLists(array $giftLists): bool {

        $count = sizeof($giftLists);

        if(!$count) return false;

        // foreach($giftLists as $giftList)
        //     if($giftList->getId() === null || $giftList->getOwnerId() === null) return false;

        $listsIds = array_map(fn(/** @param GiftList */ $giftList) => $giftList->getId(), $giftLists);
        $ownerId = array_unique(array_map(fn(/** @param GiftList */ $giftList) => $giftList->getOwner()->getId(), $giftLists));

        if(sizeof($ownerId) !== 1) return false;

        $ownerId = $ownerId[0];

        try {

            $stmt = $this->database->getConnection()->
                prepare('DELETE FROM lists WHERE user_id = ? AND list_id IN ('. implode(', ', array_fill(0, sizeof($listsIds), '?')) .')');

            $stmt->execute([$ownerId, ...$listsIds]);

            return $stmt->rowCount() == $count;

        }catch(PDOException $e) {

            return false;

        }

    }

    public function updateList(GiftList $list): bool {

        /** @var PDOStatement */
        $stmt = &$this->statements['updateList'];

        try {

            $stmt = $stmt ??
                $this->database->getConnection()->prepare('UPDATE lists SET name = :name WHERE user_id = :owner_id AND list_id = :id');

            $stmt->execute([
                "owner_id" => $list->getOwner()->getId(),
                "id" => $list->getId(),
                "name" => $list->getName()
            ]);

            return $stmt->rowCount();

        }catch(PDOException $e) {

            return false;

        }

    }

}
