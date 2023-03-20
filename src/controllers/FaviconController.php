<?

require_once 'AppController.php';

class FaviconController extends AppController {

    public function get(): void {

        header('Content-Type: image/x-icon');
        readfile('public/favicon.ico');

    }

}