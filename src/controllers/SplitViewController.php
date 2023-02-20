<?

require_once "AppController.php";

abstract class SplitViewController extends AppController {

    public function __construct() {

        parent::__construct();

        $this->addShortcode("messages", function($args) {

            return $this->templateToString("components/messages", $args);

        });

        $this->addViewFromTemplate("split", "page", ["page" => "pages/split", "appendStyles" => ["public/css/split"]]);

    }

}