<?

require_once "Manager.php";
require_once __DIR__ . "/../models/GiftResult.php";

class GiftManager extends Manager {

    /** @var PDOStatement[] */
    private array $statements;
    private string $stmt = '
            SELECT gifts.id, gifts.list_id, owners.id owner_id, owners.email owner_email,
                owners.password owner_password, owners_roles.id owner_role_id, owners_roles.name owner_role_name,
                gift_lists.name list_name, gifts.name, gifts.image, gifts.price, gifts.description, takens.id taken_by_id,
                takens.email taken_by_email, takens.password taken_by_password, takens_roles.id taken_by_role_id,
                takens_roles.name taken_by_role_name
            FROM gifts
            JOIN gift_lists ON gifts.list_id = gift_lists.id
            JOIN users owners ON gift_lists.owner_id = owners.id
            JOIN roles owners_roles ON owners.role_id = owners_roles.id
            JOIN users takens ON gifts.taken_by_id = takens.id
            JOIN roles takens_roles ON takens.role_id = takens_roles.id
        ';

    public function getGift(Gift $gift): ?Gift {

        // if($gift->getId() === null) return null;

        if(!isset($this->statements["getGift"])) {

            $this->statements["getGift"] = $this->database->getConnection()->prepare("
                $this->stmt
                WHERE gifts.id = :id
            ");
            $this->statements["getGift"]->setFetchMode(PDO::FETCH_CLASS, "GiftResult");

        }

        if(!$this->statements["getGift"]->execute(["id" => $gift->getId()])) return null;

        if(!$giftResult = $this->statements["getGift"]->fetch()) return null;

        return $giftResult->toGift();

        // return new Gift(
        //     $gift["id"],
        //     $gift["list_id"],
        //     $gift["name"],
        //     $gift["image"],
        //     $gift["price"],
        //     $gift["description"],
        //     $gift["taken_by_id"]
        // );

    }

    public function getGifts(GiftList $list): ?array {

        if($list->getId() === null) return null;

        if(!isset($this->statements["getGifts"])) {

            $this->statements["getGifts"] = $this->database->getConnection()->prepare("
                $this->stmt
                WHERE gifts.list_id = :list_id
            ");
            $this->statements["getGifts"]->setFetchMode(PDO::FETCH_CLASS, 'GiftResult');

        }

        if(!$this->statements["getGifts"]->execute(["list_id" => $list->getId()])) return null;

        if(!$gifts = $this->statements["getGifts"]->fetchAll()) return null;

        return array_map(function($giftResult) {
                return $giftResult->toGift();
            }, $gifts);

    }

    public function getUserGifts(User $user): ?array {

        // if($user->getId() === null) return null;

        if(!isset($this->statements["getUserGifts"])) {

            $this->statements["getUserGifts"] = $this->database->getConnection()->prepare("
                $this->stmt
                WHERE owner_id = :owner_id
            ");
            $this->statements["getUserGifts"]->setFetchMode(PDO::FETCH_CLASS, "GiftResult");

        }

        if(!$this->statements["getUserGifts"]->execute(["owner_id" => $user->getId()])) return null;

        if(!$gifts = $this->statements["getUserGifts"]->fetchAll()) return null;

        return array_map(function($giftResult) {
                return $giftResult->toGift();
            }, $gifts);

    }

    public function getTakenGifts(User $user): ?array {

        // if($user->getId() === null) return null;

        if(!isset($this->statements["getTakenGifts"])) {

            $this->statements["getTakenGifts"] = $this->database->getConnection()->prepare("
                $this->stmt
                WHERE taken_by_id = :taken_by_id
            ");
            $this->statements["getTakenGifts"]->setFetchMode(PDO::FETCH_CLASS, "GiftResult");

        }

        if(!$this->statements["getTakenGifts"]->execute(["taken_by_id" => $user->getId()])) return null;

        if(!$gifts = $this->statements["getTakenGifts"]->fetchAll()) return null;

        return array_map(function($giftResult) {
                return $giftResult->toGift();
            }, $gifts);

    }

    public function insertGift(Gift $gift): int {

        // if($gift->getId() === null ||
        //    $gift->getOwnerId() === null ||
        //    $gift->getName() === null ||
        //    $gift->getImage() === null ||
        //    $gift->getPrice() === null ||
        //    $gift->getDescription() === null) return false;

        $this->statements["insertGift"] = $this->statements["insertGift"] ??
            $this->database->getConnection()->prepare("
                    INSERT INTO gifts (list_id, name, image, price, description, taken_by_id)
                    VALUES (:list_id, :name, :image, :price, :description, :taken_by_id)
                ");

        return $this->statements["insertGift"]->execute([
                "list_id" => $gift->getGiftList()->getId(),
                "name" => $gift->getName(),
                "image" => $gift->getImage(),
                "price" => $gift->getPrice(),
                "description" => $gift->getDescription(),
                "taken_by_id" => $gift->getTakenBy()->getId()
            ]) ?
                $this->database->getConnection()->lastInsertId() :
                -1;

    }

    public function updateGift(Gift $gift): bool {

        // if($gift->getId() === null ||
        //    $gift->getOwnerId() === null ||
        //    $gift->getName() === null ||
        //    $gift->getImage() === null ||
        //    $gift->getPrice() === null ||
        //    $gift->getDescription() === null) return false;

        $this->statements["updateGift"] = $this->statements["updateGift"] ??
            $this->database->getConnection()->prepare("
                    UPDATE gifts
                    SET list_id = :list_id,
                        name = :name,
                        image = :image,
                        price = :price,
                        description = :description,
                        taken_by_id = :taken_by_id
                    WHERE id = :id
                ");

        return $this->statements["updateGift"]->execute([
                "list_id" => $gift->getGiftList()->getId(),
                "name" => $gift->getName(),
                "image" => $gift->getImage(),
                "price" => $gift->getPrice(),
                "description" => $gift->getDescription(),
                "taken_by_id" => $gift->getTakenBy()->getId(),
                "id" => $gift->getId()
            ]);

    }

    public function deleteGift(Gift $gift): bool {

        // if($gift->getId() === null) return false;

        $this->statements["deleteGift"] = $this->statements["deleteGift"] ??
            $this->database->getConnection()->prepare("DELETE FROM gifts WHERE id = ?");

        return $this->statements["deleteGift"]->execute(["id" => $gift->getId()]);

    }

}