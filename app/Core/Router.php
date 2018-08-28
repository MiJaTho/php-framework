<?php declare(strict_types=1);

require_once PATH . 'Core/Request.php';

class Router
{

    /**
     * The list of data types for route wildcards
     *
     * @var array
     */
    private $patterns = [
        'number' => '\d+',
        'string' => '\w+'
    ];

    /**
     * The array of available routes
     *
     * @var array
     */
    private $routes = [];

    /**
     * Create a new Router instance.
     *
     * @param  array|null  $routes
     */
    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * Handle an incoming HTTP request.
     *
     * @param  Request $request
     * @return string
     */
    public function handle(Request $request) : string
    {
        $path = $request->path();

        foreach ($this->routes as $route => $config) {

            list($method, $route) = explode('::', $route);

            if ($request->method() === $method || 'ALL' === $method) {

                $regexRoute = $this->regexRoute($route, $config);

                if (preg_match("@^/$regexRoute$@", $path)) {
                    return $this->executeController(
                        $route, $path, $config, $request
                    );
                }
            }
        }

        //$errorController = new ErrorController($request);
        //return $errorController->notFound();
        return "NOT FOUND!";
    }

    /**
     * Convert a route declaration
     *   /post/:id/edit
     * with params
     *   'id' => 'number'
     * to a regular expression
     *   /post/\d+/edit
     * that can be matched against the actual URL route
     *   /post/123/edit
     *
     * @param  string $route
     * @param  array $config
     * @return string
     */
    private function regexRoute(string $route, array $config) : string
    {
        if (isset($config['params'])) {

            foreach ($config['params'] as $wildcard => $datatype) {

                $route = str_replace(
                    ':' . $wildcard,
                    $this->patterns[$datatype],
                    $route
                );
            }
        }

        return $route;
    }

    /**
     * Get an array of all route parameters
     *
     * @param  string $route
     * @param  string $path
     * @return array
     */
    protected function routeParams(string $route, string $path, array $params) : array
    {
        $pathParts = explode('/', $path);
        array_shift($pathParts);
        
        $routeParts = explode('/', $route);
        
        foreach ($routeParts as $key => $routePart) {

            if (strpos($routePart, ':') === 0) {
                $name = substr($routePart, 1);

                $params[$name] =
                    ($params[$name] === 'number')
                        ? intval($pathParts[$key])
                        : $pathParts[$key];
            }
        }

        return $params;
    }
    
    /**
     * Execute the controller method with the route parameters
     * for a given route.
     * 
     * @param  string $route  
     * @param  string $path
     * @param  array $config
     * @param  Request $request
     * @return string
     * @throws Exception Controllers/{$controllerName}.php not found
     */
    private function executeController(
        string $route,
        string $path,
        array $config,
        Request $request
    ) : string
    {
        $controllerName = $config['controller'] . 'Controller';
        $controllerPath = PATH . "Controllers/{$controllerName}.php";
        $user = $request->session()->getInt('user');

        // If this is a login route and the user is not logged in
        // redirect to LoginController::index()
        if (isset($config['login']) && $config['login'] && !$user) {
            require_once PATH . "Controllers/LoginController.php";
            $login = new LoginController($request);
            return $login->create();
        }

        if (!file_exists($controllerPath)) {
            throw new Exception("Controllers/{$controllerName}.php not found.");
        }

        require_once $controllerPath;
        $controller = new $controllerName($request);

        if ($user) {
            $controller->setUser($user);
        }

        $confParams = $config['params'] ?? [];
        $params = $this->routeParams($route, $path, $confParams);

        return call_user_func_array(
            [$controller, $config['action']], $params
        );
    }
}