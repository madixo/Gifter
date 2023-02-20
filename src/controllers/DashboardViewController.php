<?

require_once "AppController.php";

class DashboardViewController extends AppController {

    public function __construct() {

        parent::__construct();

        $this->addShortcode("profile", function($args) {

            return $this->templateToString("components/profile", $args);

        });

        $this->addViewFromTemplate("page", "page", ["page" => "pages/dashboard", "appendStyles" => ["public/css/dashboard"], "user" => $this->sessionManager->getCurrentUser()]);

    }

}