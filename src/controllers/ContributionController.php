<?

require_once "RequestProcessor.php";

class ContributionController extends RequestProcessor {

    public function put() {

        $this->safeChecks();

        $this->assertOrDie(isset($this->json["data"]), 400);

        die(
            json_encode(
                ($result = $this->contributionManager->addContribution(new Contribution($this->sessionManager->getCurrentUser(), new GiftList(null, null, null, $this->json["data"])))) ?
                    ["status" => true, "list_info" => ["id" => $result->getId(), "name" => $result->getName()]] :
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
                    $this->contributionManager->deleteContribution(new Contribution($this->sessionManager->getCurrentUser(), new GiftList($this->json["id"], null, null, null))) ?
                        ["status" => true] :
                        ["status" => false, "message" => "error"]
                )
            );

        }else if(isset($this->json["ids"])) {

            die(
                json_encode(
                    $this->contributionManager->deleteContributions(array_map(fn($id) => new Contribution($this->sessionManager->getCurrentUser(), new GiftList($id, null, null, null)), $this->json["ids"])) ?
                        ["status" => true] :
                        ["status" => false, "message" => "error"]
                )
            );

        }

    }

}