<?

require_once "Manager.php";
require_once __DIR__ . "/../models/GiftListResult.php";

class GiftListManager extends Manager {

    /** @var PDOStatement[] */
    private array $statements = [];
    private string $listsStmt = '
        SELECT gift_lists.id, gift_lists.name, gift_lists.owner_id, users.email owner_email,
            users.password owner_password, users.role_id owner_role_id, roles.name owner_role_name,
            gift_lists.access_code
        FROM gift_lists
            JOIN users ON gift_lists.owner_id = users.id
            JOIN roles on users.role_id = roles.id
    ';

    public function insertList(GiftList $list): ?GiftList {

        // if($list->getName() === null || $list->getOwner() === null || $list->getOwner()->getId() === null) return false;

        if(!isset($this->statements["insertList"])) {

            $this->statements["insertList"] = $this->database->getConnection()->prepare("
                INSERT INTO gift_lists (owner_id, name) VALUES (:owner_id, :name) RETURNING id, name, access_code
            ");

            $this->statements["insertList"]->setFetchMode(PDO::FETCH_CLASS, 'GiftListResult');

        }

        if(!$this->statements["insertList"]->execute(["owner_id" => $list->getOwner()->getId(), "name" => $list->getName()])) return null;

        if(!$result = $this->statements["insertList"]->fetch()) return null;

        return $result->toGiftList();

    }

    public function getList(GiftList $list): ?GiftList {

        if($list->getOwner() !== null && $list->getId() !== null) return $this->getListAuth($list);

        if($list->getId() !== null) return $this->getListById($list);

        if($list->getAccessCode() !== null) return $this->getListByAccessCode($list);

        return null;

    }

    public function getListAuth(GiftList $list): ?GiftList {

        // if($list->getId() === null || $list->getOwner() === null || $list->getOwner()->getId() === null) return null;

        if(!isset($this->statements["getListById"])) {

            $this->statements["getListById"] = $this->database->getConnection()->prepare("
                $this->listsStmt
                WHERE gift_lists.id = :id AND owner_id = :owner_id
            ");
            $this->statements["getListById"]->setFetchMode(PDO::FETCH_CLASS, 'GiftListResult');

        }

        if(!$this->statements["getListById"]->execute(["id" => $list->getId(), "owner_id" => $list->getOwner()->getId()])) return null;

        if(!$giftListResult = $this->statements["getListById"]->fetch()) return null;

        return $giftListResult->toGiftList();

    }

    public function getListById(GiftList $list): ?GiftList {

        // if($list->getId() === null || $list->getOwner() === null || $list->getOwner()->getId() === null) return null;

        if(!isset($this->statements["getListById"])) {

            $this->statements["getListById"] = $this->database->getConnection()->prepare("
                $this->listsStmt
                WHERE gift_lists.id = :id
            ");
            $this->statements["getListById"]->setFetchMode(PDO::FETCH_CLASS, 'GiftListResult');

        }

        if(!$this->statements["getListById"]->execute(["id" => $list->getId()])) return null;

        if(!$giftListResult = $this->statements["getListById"]->fetch()) return null;

        return $giftListResult->toGiftList();

    }

    public function getListByAccessCode(GiftList $list) {

        if(!isset($this->statements["getListByAccessCode"])) {

            $this->statements["getListByAccessCode"] = $this->database->getConnection()->prepare("
                $this->listsStmt
                WHERE gift_lists.access_code = :access_code
            ");
            $this->statements["getListByAccessCode"]->setFetchMode(PDO::FETCH_CLASS, 'GiftListResult');

        }

        if(!$this->statements["getListByAccessCode"]->execute(["access_code" => $list->getAccessCode()])) return null;

        if(!$giftListResult = $this->statements["getListByAccessCode"]->fetch()) return null;

        return $giftListResult->toGiftList();

    }

    public function getUserLists(User $user): ?array {

        // if($user->getId() === null) return null;

        if(!isset($this->statements["getUserLists"])) {

            $this->statements["getUserLists"] = $this->database->getConnection()->prepare("
                $this->listsStmt
                WHERE gift_lists.owner_id = :owner_id
            ");
            $this->statements["getUserLists"]->setFetchMode(PDO::FETCH_CLASS, 'GiftListResult');

        }

        if(!$this->statements["getUserLists"]->execute(["owner_id" => $user->getId()])) return null;

        if(!$giftListResults = $this->statements["getUserLists"]->fetchAll()) return null;

        return array_map(function(/** @var GiftListResult */ $giftListResult) {
                return $giftListResult->toGiftList();
            }, $giftListResults);

    }

    public function deleteList(GiftList $list): bool {

        // if($list->getId() === null || $list->getOwnerId() === null) return false;

        $this->statements["deleteList"] = $this->statements["deleteList"] ??
            $this->database->getConnection()->prepare("DELETE FROM gift_lists WHERE id = :id AND owner_id = :owner_id");

        return $this->statements["deleteList"]->execute(["id" => $list->getId(), "owner_id" => $list->getOwner()->getId()]);

    }

    /**
     * @param GiftList[] $giftLists
     */
    public function deleteLists(array $giftLists): bool {

        if(!sizeof($giftLists)) return false;

        // foreach($giftLists as $giftList)
        //     if($giftList->getId() === null || $giftList->getOwnerId() === null) return false;

        $listsIds = array_map(function(/** @var GiftList */ $giftList) { return $giftList->getId(); }, $giftLists);
        $ownerId = array_unique(array_map(function(/** @var GiftList */ $giftList) { return $giftList->getOwner()->getId(); }, $giftLists));

        if(sizeof($ownerId) !== 1) return false;

        $ownerId = $ownerId[0];

        return $this->database->getConnection()->
            prepare("DELETE FROM gift_lists WHERE owner_id = ? AND id IN (". implode(", ", array_fill(0, sizeof($listsIds), "?")) .")")->
            execute([
                $ownerId,
                ...$listsIds
            ]);

    }

}