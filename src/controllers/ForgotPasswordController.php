<?

require_once "SplitViewController.php";

class ForgotPasswordController extends SplitViewController {

    public function __construct() {

        parent::__construct();

        $this->addViewFromView("forgot_password", "split", ["panel" => "panels/forgot_password_panel"]);

    }

    public function get(): void {

        $this->addShortcode("email", function($args) {

            return $this->templateToString("components/inputs/email", $args);

        });

        $this->renderView("forgot_password");

    }

    public function post(): void {

        $response = $this->userManager->requestPasswordReset(new User(null, $this->args["email"] ?? null, null, null));

        $stmt = $this->userManager->getDatabase()->getConnection()->prepare("select uuid from users join password_reset on users.id = user_id where email = ?");
        $stmt->execute([$this->args["email"]]);

        $uuid = $stmt->fetch()["uuid"];

        $this->renderView("forgot_password", $response ?
            ["message" => "Jeśli istnieje konto przypisane do podanego emaila, zostanie wysłany link do zresetowania hasła. <br> DEBUG: <a href=\"http://localhost:8080/reset-password?uuid=$uuid\">link</a>"] :
            ["error" => "Wystąpił błąd podczas wysyłania linku."]
        );

    }

}