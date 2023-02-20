<?

// phpinfo();

define('GIFTER_APP', __DIR__);

require_once "src/Router.php";
foreach(glob(__DIR__ . "/src/controllers/*.php") as $controller)
    require_once $controller;
require_once "src/managers/SessionManager.php";

Router::not_found('DefaultController');

Router::get('/login', 'LoginController');
Router::post('/login', 'LoginController');

Router::get('/register', 'RegisterController');
Router::post('/register', 'RegisterController');

Router::get('/forgot-password', 'ForgotPasswordController');
Router::post('/forgot-password', 'ForgotPasswordController');

Router::get('/reset-password', 'ResetPasswordController');
Router::post('/reset-password', 'ResetPasswordController');

Router::get('/code', 'CodeController');
Router::post('/code', 'CodeController');

Router::get('/dashboard', 'DashboardController');

Router::get('/list', 'ListController');
Router::put('/list', 'ListController');
Router::update('/list', 'ListController');
Router::delete('/list', 'ListController');

Router::get('/editList', 'EditListController');

Router::put('/contribution', 'ContributionController');
Router::delete('/contribution', 'ContributionController');

Router::get('/terms-of-use', 'TermsOfUseController');

Router::run();