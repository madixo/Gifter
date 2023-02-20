<?

require_once "SplitViewController.php";

class RegisterController extends SplitViewController {

    public function __construct() {

        parent::__construct();

        $this->addShortcode("password", function($args) {

            return $this->templateToString("components/inputs/password", $args);

        });

        $this->addShortcode("email", function($args) {

            return $this->templateToString("components/inputs/email", $args);

        });

        $this->addViewFromView("register", "split", [
            "panel" => "panels/register_panel",
            "appendScripts" => [["src" => "public/scripts/validate_password", "defer" => "true"]]
        ]);

    }

    public function get(): void {

        if($this->sessionManager->isAuthenticated())
            Router::route('/dashboard');

        $this->renderView("register");

    }

    public function post(): void {

        $result = $this->userManager->insertUser(new User(null, $this->args["email"] ?? null, $this->args["password"] ?? null, null));

        if($result["status"])
            Router::route("/login", ["m" => base64_encode("Pomyślnie założono konto!")]);
        else
            $this->renderView("register", ["email" => $this->args["email"], "error" => $result["message"]]);

    }

}