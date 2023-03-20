<?

require_once 'RequestProcessor.php';

class ItemController extends RequestProcessor {

    public function put() {

        $this->safeChecks();

        $this->assertOrDie(isset($this->json['data']), 400);

    }

    public function delete() {

        $this->safeChecks();

        $this->assertOrDie(isset($this->json['id']) xor (isset($this->json['ids']) && sizeof($this->json['ids'])), 400);

    }

    public function update() {

        $this->safeChecks();

        $this->assertOrDie(isset($this->json['id']), 400);

    }

}