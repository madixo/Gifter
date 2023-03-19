<?

require_once "SplitViewController.php";

class CodeController extends SplitViewController {

    public function __construct() {

        parent::__construct();

        $this->addShortcode("email", function($args) {

            return $this->templateToString("components/input/email", $args);

        });

    }

    public function get(): void {

        $this->renderView("split", ["panel" => "code"]);

    }

    public function post(): void {



    }

}
