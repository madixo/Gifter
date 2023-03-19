<?

require_once "RequestProcessor.php";

class ListController extends RequestProcessor {

    public function get(): void {

        if(!isset($this->query["id"]) &&
        !$list = $this->contributionManager->getContribution(new Contribution($this->sessionManager->getCurrentUser(), new GiftList($this->query["id"], null, null, null))))
            Router::route("/dashboard");

        $this->renderView("page", ["panel" => "list", "name" => $list->getName()]);

    }

    public function put(): void {

        $this->safeChecks();

        $this->assertOrDie(isset($this->json["data"]), 400);

        die(
            json_encode(
                ($result = $this->giftListManager->insertList(new GiftList(null, $this->sessionManager->getCurrentUser(), $this->json["data"], null))) ?
                ["status" => true, "list_info" => ["id" => $result->getId(), "name" => $result->getName(), "access_code" => $result->getAccessCode()]] :
                ["status" => false, "message" => "error"]
            )
        );

    }

    public function delete() {

        $this->safeChecks();

        $this->assertOrDie(isset($this->json["id"]) xor (isset($this->json["ids"]) && sizeof($this->json["ids"])), 400);

        if(isset($this->json["id"])) {

            die(
                json_encode(
                    $this->giftListManager->deleteList(new GiftList($this->json["id"], $this->sessionManager->getCurrentUser(), null, null)) ?
                        ["status" => true] :
                        ["status" => false, "message" => "Wystąpił błąd podczas usuwania listy!"]
                )
            );

        }else if(isset($this->json["ids"])) {

            die(
                json_encode(
                    $this->giftListManager->deleteLists(array_map(fn($id) => new GiftList($id, $this->sessionManager->getCurrentUser(), null, null), $this->json["ids"])) ?
                        ["status" => true] :
                        ["status" => false, "message" => "Wystąpił błąd podczas usuwania list!"]
                )
            );

        }

    }

}
