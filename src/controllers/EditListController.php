<?

require_once "DashboardViewController.php";

class EditListController extends DashboardViewController {

    public function get() {

        if(!isset($this->query["id"])) Router::route("/dashboard");

        $list = $this->giftListManager->getList(new GiftList($this->query["id"], $this->sessionManager->getCurrentUser(), null, null));

        if(!isset($list)) Router::route("/dashboard");

        $this->renderView("page", ["panel" => "panels/edit_list_panel", "name" => htmlspecialchars($list->getName()), "gifts" => $this->giftManager->getGifts($list)]);

    }

}