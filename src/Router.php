<?

class Router {

    static private array $routes = [
        "GET" => [],
        "POST" => [],
        "DELETE" => [],
        "UPDATE" => [],
        "PUT" => []
    ];

    static private string $not_found;

    static public function get(string $route, string $controller) {

        self::add_route("GET", $route, $controller);

    }

    static public function post(string $route, string $controller) {

        self::add_route("POST", $route, $controller);

    }

    static public function delete(string $route, string $controller) {

        self::add_route("DELETE", $route, $controller);

    }

    static public function update(string $route, string $controller) {

        self::add_route("UPDATE", $route, $controller);

    }

    static public function put(string $route, string $controller) {

        self::add_route("PUT", $route, $controller);

    }

    static public function not_found(string $controller) {

        self::$not_found = $controller;

    }

    static public function add_route(string $method, string $route, string $controller) {

        if(array_key_exists($method, self::$routes))
            self::$routes[$method][$route] = $controller;

    }

    static public function route(string $route, array $args = null) {

        header("Location: $route" . (isset($args) ? "?" . http_build_query($args, "", "&") : ""));
        die();

    }

    static public function run() {

        $path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

        if(strlen($path) > 1)
            $path = rtrim($path, "/");

        if(array_key_exists($path, self::$routes[$_SERVER["REQUEST_METHOD"]])) {

            $controller = self::$routes[$_SERVER["REQUEST_METHOD"]][$path];

        }else {

            if($_SERVER["REQUEST_METHOD"] === "GET")
                $controller = self::$not_found;
            else {
                http_response_code(404);
                die();
            }

        }

        (new $controller())->{$_SERVER["REQUEST_METHOD"]}($path);

    }

}