<?

require_once "AppController.php";

class NotFoundController extends AppController {

    public function get(): void {

        print_r("404");

    }

}