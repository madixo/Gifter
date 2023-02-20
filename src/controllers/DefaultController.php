<?

require_once "AppController.php";

class DefaultController extends AppController {

    public function fallback(): void {

        Router::route("/login");

    }

}