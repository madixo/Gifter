<?

require_once __DIR__ . "/../managers/UserManager.php";
require_once __DIR__ . "/../managers/SessionManager.php";
require_once __DIR__ . "/../managers/GiftManager.php";
require_once __DIR__ . "/../managers/GiftListManager.php";
require_once __DIR__ . "/../managers/ContributionManager.php";

abstract class AppController {

    private string $method;
    protected array $query = [];
    protected array $args;
    protected ?array $json;
    protected Database $database;
    protected UserManager $userManager;
    protected GiftManager $giftManager;
    protected GiftListManager $giftListManager;
    protected SessionManager $sessionManager;
    protected ContributionManager $contributionManager;
    private array $shortcodes = [];
    private array $views = [];

    public function __construct() {

        parse_str($_SERVER["QUERY_STRING"], $this->query);
        $this->args = $_POST;
        if($_SERVER['HTTP_CONTENT_TYPE'] ?? "" === "application/json")
            $this->json = json_decode(file_get_contents('php://input'), null, 512, JSON_OBJECT_AS_ARRAY);
        $this->database = new Database(DB_USER, DB_PASSWORD, DB_HOST, DB_PORT, DB_NAME);
        $this->userManager = new UserManager($this->database);
        $this->giftManager = new GiftManager($this->database);
        $this->giftListManager = new GiftListManager($this->database);
        $this->sessionManager = new SessionManager($this->userManager);
        $this->contributionManager = new ContributionManager($this->giftListManager);
        $this->sessionManager->checkSession();

    }

    protected function templateToString(string $template, array $variables = []): string {

        $templatePath = "public/views/" . $template . ".php";
        $output = "File not found " . $templatePath;

        if(file_exists($templatePath)) {

            extract($variables);

            ob_start();

            require $templatePath;
            $output = ob_get_clean();

            // FIXME: infinite recursion
            foreach($this->shortcodes as $shortcode => $function) {

                preg_match_all("/\[$shortcode( (([a-zA-Z0-9_\-ąęćńśźóżł]+)(\=(?|[$]?(?3)|('[a-zA-Z0-9_\-ąęćńśźóżł ]+')))*)+)*\]/i", $output, $matches);

                foreach($matches[0] as $match) {

                    preg_match_all("/(?<matches>(?<= )(?<keys>[a-zA-Z0-9_\-ąęćńśźóżł]+)(?:\=(?<values>(?|[$]?(?2)|(?:'[a-zA-Z0-9_\-ąęćńśźóżł ]+'))))*)+/i", $match, $args, PREG_UNMATCHED_AS_NULL);

                    $output = str_replace(
                        $match,
                        $function(
                            isset($args["matches"]) ?
                                array_combine(
                                    $args["keys"],
                                    array_map(function($value) use($variables) {
                                        $value = $value ? $value = trim($value, "'") : true;
                                        if(str_starts_with($value, '$'))
                                            return $variables[substr($value, 1)] ?? null;
                                        return $value;
                                    }, $args["values"])
                                ) : []
                        ),
                        $output
                    );

                }

            }

        }

        return $output;

    }

    protected function addViewFromTemplate(string $viewName, string $template, array $variables = []): void {

        $this->views[$viewName]["template"] = $template;
        $this->views[$viewName]["variables"] = $variables;

    }

    protected function addViewFromView(string $viewName, string $oldViewName, array $variables = []): void {

        $this->views[$viewName]["template"] = $this->views[$oldViewName]["template"];
        $this->views[$viewName]["variables"] = array_merge($this->views[$oldViewName]["variables"], $variables);

    }

    protected function renderTemplate(string $template, array $variables = []): void {

        print $this->templateToString($template, $variables);

    }

    protected function renderView(string $view, array $additionalVariables = []): void {

        if(!array_key_exists($view, $this->views)) die("Cannot find view: $view");

        $this->renderTemplate($this->views[$view]["template"], array_merge($this->views[$view]["variables"], $additionalVariables));

    }

    protected function addShortcode(string $shortcode, callable $callback): void {

        $this->shortcodes[$shortcode] = $callback;

    }

    public function fallback(): void { die("not implemented"); }

    public function __call(string $name, array $arguments) { $this->fallback(); }

}