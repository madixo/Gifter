<?

require_once "DashboardViewController.php";

class DashboardController extends DashboardViewController {

    public function __construct() {

        parent::__construct();

        $this->addShortcode("dashboard_list", function($args) {

            return $this->templateToString("components/dashboard_list", $args);

        });

    }

    public function get(): void {

        if(!$this->sessionManager->isAuthenticated())
            Router::route('/login');

        $myLists = $this->giftListManager->getUserLists($this->sessionManager->getCurrentUser());
        $otherLists = $this->contributionManager->getUserContributions($this->sessionManager->getCurrentUser());

        $this->renderView("page", [
            "panel" => "panels/dashboard_panel",
            "name" => "Dashboard",
            "appendScripts" => [
                ["src" => "public/scripts/dashboard", "defer" => true]
            ],
            "passDataToFront" => [
                "csrfToken" => "'" . Utils::generateCSRFToken($this->sessionManager->getSessionUUID()) . "'"
            ],
            "myLists" => array_map(fn($list) => ["id" => $list->getId(), "name" => $list->getName(), "access_code" => $list->getAccessCode()], $myLists ?? []),
            "otherLists" => array_map(fn($list) => ["id" => $list->getId(), "name" => $list->getName()], $otherLists ?? [])
        ]);

    }

}