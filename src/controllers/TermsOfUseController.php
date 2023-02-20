<?

require_once "SplitViewController.php";

class TermsOfUseController extends SplitViewController {

    public function get(): void {

        $this->renderView("split", ["panel" => "panels/terms_of_use_panel"]);

    }

}