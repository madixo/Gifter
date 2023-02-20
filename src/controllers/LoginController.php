<?

require_once "SplitViewController.php";

class LoginController extends SplitViewController {

    public function __construct() {

        parent::__construct();

        $this->addShortcode("password", function($args) {

            return $this->templateToString("components/inputs/password", $args);

        });

        $this->addShortcode("email", function($args) {

            return $this->templateToString("components/inputs/email", $args);

        });

        $this->addViewFromView("login", "split", ["panel" => "panels/login_panel"]);

    }

    public function get(): void {

        if($this->sessionManager->isAuthenticated())
            Router::route('/dashboard');

        $this->renderView("login", [
                "message" => isset($this->query["m"]) ? base64_decode($this->query["m"]) : null
            ]);

    }

    public function post(): void {

        $user = $this->userManager->getUser(new User(null, $this->args["email"] ?? null, null, null));

        if($user === null || $this->args["password"] === null || !password_verify($this->args["password"], $user->getPassword()))
            $this->renderView("login", ["error" => "Login lub hasło nieprawidłowe!", "email" => $this->args["email"] ?? null]);
        else {

            if($this->sessionManager->createSession($user))
                Router::route("/dashboard");
            else
                $this->renderView("login", ["error" => "Błąd podczas tworzenia sesji!", "email" => $this->args["email"] ?? null]);

        }

    }

}