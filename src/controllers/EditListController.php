<?

require_once 'RequestProcessor.php';

class EditListController extends RequestProcessor {

    public function __construct() {

        parent::__construct();

        $this->addShortcode('list', fn(array $args) =>
            $this->templateToString('components/list/default', [
                ...$args,
                'templates' => [
                    'buttons' => 'addable',
                    'entry_buttons' => 'edit_list'
                ]
            ])
        );

        $this->addShortcode('input_group', fn(array $args) =>
            $this->templateToString('components/input_group/default', [
                ...$args,
                'templates' => [
                    'group' => 'list_name'
                ]
            ])
        );

    }

    public function get() {

        if(!$this->sessionManager->isAuthenticated()) Router::route('/login');

        if(!isset($this->query['id'])) Router::route('/dashboard');

        $list = $this->giftListManager->getList(new GiftList($this->query['id'], $this->sessionManager->getCurrentUser(), null, null));

        if(!isset($list)) Router::route('/dashboard');

        $gifts = $this->giftManager->getGifts($list);

        $this->renderView('page', [
            'panel' => 'edit_list',
            'name' => htmlspecialchars($list->getName()),
            'items' => array_map(fn(Gift $gift) => ['data' => ['id' => $gift->getId()]], $gifts),
            'appendScripts' => [
                ['src' => 'edit_list', 'defer' => true],
                ['src' => 'addable_list', 'defer' => true]
            ],
            'passDataToFront' => [
                'csrfToken' => "'" . Utils::generateCSRFToken($this->sessionManager->getSessionID()) . "'",
                'listId' => "'{$list->getId()}'"
            ],
        ]);

    }

    public function update() {

        $this->safeChecks();

        $this->assertOrDie(isset($this->json['id']) && isset($this->json['name']), 400);

        die(
            json_encode(
                $this->giftListManager->updateList(new GiftList($this->json['id'], $this->sessionManager->getCurrentUser(), $this->json['name'], null)) ?
                ['status' => 1] :
                ['status' => 0, 'message' => 'error']
            )
        );

    }

}
