<?

require_once "DashboardViewController.php";

class RequestProcessor extends DashboardViewController {

    protected function assertOrDie(bool $condition, int $responseCode) {

        if(!$condition) {
            http_response_code($responseCode);
            die();
        }

    }

    protected function safeChecks(): void {

        $this->assertOrDie($this->sessionManager->isAuthenticated(), 401);

        $this->assertOrDie(isset($this->json) || isset($this->json['csrfToken']), 400);

        $this->assertOrDie(Utils::validateCSRFToken($this->sessionManager->getSessionUUID(), $this->json["csrfToken"]), 403);

    }

}