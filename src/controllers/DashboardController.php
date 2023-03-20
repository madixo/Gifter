<?

require_once __DIR__ . "/../config.php";

require_once "DashboardViewController.php";

class DashboardController extends DashboardViewController {

    public function __construct() {

        parent::__construct();

        $this->addShortcode("list", fn(array $args) =>
            $this->templateToString("components/list/default", [
                ...$args,
                "templates" => [
                    "entry_buttons" => "dashboard",
                    "buttons" => "addable"
                ]
            ])
        );

    }

    public function get(): void {

        if(!$this->sessionManager->isAuthenticated())
            Router::route('/login');

        $myLists = $this->giftListManager->getUserLists($this->sessionManager->getCurrentUser());
        $contributedLists = $this->contributionManager->getContributedLists($this->sessionManager->getCurrentUser());

        $this->renderView("page", [
            "panel" => "dashboard",
            "name" => "Dashboard",
            "appendScripts" => [
                ["src" => "addable_list", "defer" => true],
                ["src" => "dashboard", "defer" => true]
            ],
            "passDataToFront" => [
                "csrfToken" => "'" . Utils::generateCSRFToken($this->sessionManager->getSessionID()) . "'"
            ],
            "myLists" => array_map(fn(/** @var GiftList */ $list) => ["data" => ["id" => $list->getId()], "contents" => [$list->getName(), str_pad($list->getAccessCode(), LIST_CODE_LENGTH, "0", STR_PAD_LEFT)]], $myLists ?? []),
            "otherLists" => array_map(fn(/** @var GiftList */ $contributedList) => ["data" => ["id" => $contributedList->getId()], "contents" => [$contributedList->getName()]], $contributedLists ?? [])
        ]);

    }

}
