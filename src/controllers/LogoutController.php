<?

require_once 'AppController.php';

class LogoutController extends AppController {

    public function get(): void {

        $this->sessionManager->destroySession();

        Router::route('/login');

    }

}