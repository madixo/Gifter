<?

require_once __DIR__ . "/../Database.php";
require_once __DIR__ . "/../config.php";

class Manager {

    public function __construct(protected Database $database) {

        $this->database->connect();

    }

    public function getDatabase(): Database {

        return $this->database;

    }

}