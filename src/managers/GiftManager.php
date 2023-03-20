<?

require_once "Manager.php";
require_once __DIR__ . "/../models/GiftResult.php";

class GiftManager extends Manager {

    /** @var PDOStatement[] */
    private array $statements;
    private string $stmt = 'select * from get_gifts';

    public function getGift(Gift $gift): ?Gift {

        // if($gift->getId() === null) return null;

        /** @var PDOStatement */
        $stmt = &$this->statements['getGift'];

        try {

            if(!isset($stmt)) {

                $stmt = $this->database->getConnection()->prepare("
                    $this->stmt
                    WHERE gifts.id = :id
                ");
                $stmt->setFetchMode(PDO::FETCH_CLASS, GiftResult::class);

            }

            $stmt->execute(['id' => $gift->getId()]);

            /** @var GiftResult $giftResult */
            if(!$giftResult = $stmt->fetch()) return null;

            return $giftResult->toGift();

        }catch(PDOException $e) {

            return null;

        }

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

    public function getGifts(GiftList $list): array {

        // if($list->getId() === null) return null;

        /** @var PDOStatement */
        $stmt = &$this->statements['getGifts'];

        try {

            if(!isset($stmt)) {

                $stmt = $this->database->getConnection()->prepare("
                    $this->stmt
                    WHERE list_id = :list_id
                ");
                $stmt->setFetchMode(PDO::FETCH_CLASS, GiftResult::class);

            }

            $stmt->execute(['list_id' => $list->getId()]);

            /** @var GiftResult[] $gifts */
            if(!$gifts = $stmt->fetchAll()) return [];

            return array_map(fn(/** @var GiftResult */$giftResult) => $giftResult->toGift(), $gifts);

        }catch(PDOException $e) {

            return [];

        }

    }

    public function getUserGifts(User $user): array {

        // if($user->getId() === null) return null;

        /** @var PDOStatement */
        $stmt = &$this->statements['getUserGifts'];

        try {

            if(!isset($stmt)) {

                $stmt = $this->database->getConnection()->prepare("
                    $this->stmt
                    WHERE owner_id = :owner_id
                ");
                $stmt->setFetchMode(PDO::FETCH_CLASS, GiftResult::class);

            }

            $stmt->execute(['owner_id' => $user->getId()]);

            /** @var GiftResult[] $gifts */
            if(!$gifts = $stmt->fetchAll()) return [];

            return array_map(fn(/** @var GiftResult */$giftResult) => $giftResult->toGift(), $gifts);

        }catch(PDOException $e) {

            return [];

        }

    }

    public function getTakenGifts(User $user): array {

        // if($user->getId() === null) return null;

        /** @var PDOStatement */
        $stmt = &$this->statements['getTakenGifts'];

        try {

            if(!isset($stmt)) {

                $stmt = $this->database->getConnection()->prepare("
                    $this->stmt
                    WHERE taken_by_id = :taken_by_id
                ");
                $stmt->setFetchMode(PDO::FETCH_CLASS, GiftResult::class);

            }

            $stmt->execute(['taken_by_id' => $user->getId()]);

            /** @var GiftResult[] $gifts */
            if(!$gifts = $stmt->fetchAll()) return [];

            return array_map(fn(/** @var GiftResult */$giftResult) => $giftResult->toGift(), $gifts);

        }catch(PDOException $e) {

            return [];

        }

    }

    public function insertGift(Gift $gift): int {

        // if($gift->getId() === null ||
        //    $gift->getOwnerId() === null ||
        //    $gift->getName() === null ||
        //    $gift->getImage() === null ||
        //    $gift->getPrice() === null ||
        //    $gift->getDescription() === null) return false;

        /** @var PDOStatement */
        $stmt = &$this->statements['insertGift'];

        try {

            $stmt = $stmt ??
                $this->database->getConnection()->prepare('
                    INSERT INTO gifts (list_id, name, image, price, description, taken_by_id)
                    VALUES (:list_id, :name, :image, :price, :description, :taken_by_id)
                ');

            $stmt->execute([
                'list_id' => $gift->getGiftList()->getId(),
                'name' => $gift->getName(),
                'image' => $gift->getImage(),
                'price' => $gift->getPrice(),
                'description' => $gift->getDescription(),
                'taken_by_id' => $gift->getTakenBy()->getId()
            ]);

            return $this->database->getConnection()->lastInsertId();

        }catch(PDOException $e ) {

            return -1;

        }

    }

    public function updateGift(Gift $gift): bool {

        // if($gift->getId() === null ||
        //    $gift->getOwnerId() === null ||
        //    $gift->getName() === null ||
        //    $gift->getImage() === null ||
        //    $gift->getPrice() === null ||
        //    $gift->getDescription() === null) return false;

        /** @var PDOStatement */
        $stmt = &$this->statements['updateGift'];

        try {

            $stmt = $stmt ??
                $this->database->getConnection()->prepare('
                    UPDATE gifts
                    SET list_id = :list_id,
                        name = :name,
                        image = :image,
                        price = :price,
                        description = :description,
                        taken_by_id = :taken_by_id
                    WHERE id = :id
                ');

            $stmt->execute([
                'list_id' => $gift->getGiftList()->getId(),
                'name' => $gift->getName(),
                'image' => $gift->getImage(),
                'price' => $gift->getPrice(),
                'description' => $gift->getDescription(),
                'taken_by_id' => $gift->getTakenBy()->getId(),
                'id' => $gift->getId()
            ]);

            return true;

        }catch(PDOException $e) {

            return false;

        }

    }

    public function deleteGift(Gift $gift): bool {

        // if($gift->getId() === null) return false;

        /** @var PDOStatement */
        $stmt = &$this->statements['deleteGift'];

        try {

            $stmt = $stmt ??
                $this->database->getConnection()->prepare("DELETE FROM gifts WHERE id = ?");

            $stmt->execute(["id" => $gift->getId()]);

            return true;

        }catch(PDOException $e) {

            return false;

        }

    }

}
