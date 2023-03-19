<?

require_once 'AppController.php';

class DashboardViewController extends AppController {

    public function __construct() {

        parent::__construct();

        $this->addShortcode('profile', function($args) {

            return $this->templateToString('components/profile', $args);

        });

        $this->addViewFromTemplate('page', 'page', ['page' => 'dashboard', 'appendStyles' => ['dashboard'], 'appendScripts' => [['src' => 'dashboard_view', 'defer' => true]], 'user' => $this->sessionManager->getCurrentUser()]);

    }

}
