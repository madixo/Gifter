<?

require_once "SplitViewController.php";

class ResetPasswordController extends SplitViewController {

    public function __construct() {

        parent::__construct();

        $this->addShortcode("password", function($args) {

            return $this->templateToString("components/inputs/password", $args);

        });

        $this->addViewFromView("reset_password", "split", [
            "panel" => "panels/reset_password_panel",
            "appendScripts" => [["src" => "public/scripts/validate_password", "defer" => true]]
        ]);

    }

    public function get(): void {

        if(!isset($this->query["uuid"])) Router::route('/');

        $this->renderView("reset_password", ["uuid" => $this->query["uuid"]]);

    }

    public function post(): void {

        $result = $this->userManager->updatePassword($this->args["uuid"], new User(null, null, $this->args["password"], null));

        if($result["status"])
            Router::route("/login", ["m" => base64_encode("Pomyślnie zmieniono hasło!")]);
        else
            $this->renderView("reset_password", ["error" => $result["message"] ?? null]);

    }

}