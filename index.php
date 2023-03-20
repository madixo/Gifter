<?

// phpinfo();

define('GIFTER_APP', __DIR__);

require_once "src/Router.php";
foreach(glob(__DIR__ . "/src/controllers/*.php") as $controller)
    require_once $controller;
require_once "src/managers/SessionManager.php";

Router::not_found('DefaultController');

Router::get('/favicon.ico', FaviconController::class);

Router::get('/login', LoginController::class);
Router::post('/login', LoginController::class);

Router::get('/register', RegisterController::class);
Router::post('/register', RegisterController::class);

Router::get('/forgot-password', ForgotPasswordController::class);
Router::post('/forgot-password', ForgotPasswordController::class);

Router::get('/reset-password', ResetPasswordController::class);
Router::post('/reset-password', ResetPasswordController::class);

Router::get('/code', CodeController::class);
Router::post('/code', CodeController::class);

Router::get('/dashboard', DashboardController::class);

Router::get('/list', ListController::class);
Router::put('/list', ListController::class);
Router::delete('/list', ListController::class);

Router::get('/edit-list', EditListController::class);
Router::update('/list', EditListController::class);

Router::put('/contribution', ContributionController::class);
Router::delete('/contribution', ContributionController::class);

Router::get('/terms-of-use', TermsOfUseController::class);

Router::get('/logout', LogoutController::class);

Router::run();
