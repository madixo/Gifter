<?

require_once "AppController.php";

abstract class SplitViewController extends AppController {

    public function __construct() {

        parent::__construct();

        $this->addShortcode('back', fn(array $args) =>
            $this->templateToString('components/back', $args)
        );

        $this->addShortcode('messages', fn(array $args) =>
            $this->templateToString('components/messages', $args)
        );

        $this->addViewFromTemplate('split', 'page', ['page' => 'split', 'appendStyles' => ['split']]);

    }

}
