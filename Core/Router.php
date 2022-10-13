<?php

namespace Core;

/**
 * Router
 */
class Router
{
    /**
     * Associative array of routes (the routing table)
     * @var array
     */
    protected $routes = [];

    /**
     * Parameters from the matched route
     * @var array
     */
    protected $params = [];

    /**
     * Request body data.
     * @var
     */
    protected $body;

    /**
     * Action of the controller.
     * @var
     */
    protected $action;

    /**
     * Name of the controller.
     * @var
     */
    protected $controllerName;

    /**
     * Add a route to the routing table
     *
     * @param string $route  The route URL
     * @param array  $params Parameters (controller, action, etc.)
     *
     * @return void
     */
    public function add($route, $params = [])
    {
        $route = str_replace('/', '', $route);
        $this->body = file_get_contents('php://input');
        $this->action = $params[1];
        $this->controllerName = $params[0];
        $this->routes[$route]['action'] = $params[1];
        $this->routes[$route]['controllerName'] = $params[0];
        $this->routes[$route]['data'] = file_get_contents('php://input');
        return $this;
    }

    /**
     * Add any route to routing table.
     *
     * @param string $route  The route URL
     * @param array  $params Parameters (controller, action, etc.)
     *
     * @return void
     */
    public function any($route, $params = []){
        $this->routes[$params[1]] = ['method' => 'any'];
        $this->add($route, $params);
    }

    /**
     * Add only GET route to routing table.
     *
     * @param string $route  The route URL
     * @param array  $params Parameters (controller, action, etc.)
     *
     * @return void|null
     */
    public function get($route, $params = []){
        $route = str_replace('/', '', $route);
        $this->routes[$route] = ['method' => 'GET'];
        return $this->add($route, $params);
    }

    /**
     * Add only POST route to routing table.
     *
     * @param string $route  The route URL
     * @param array  $params Parameters (controller, action, etc.)
     *
     * @return void|null
     */
    public function post($route, $params = []){
        $route = str_replace('/', '', $route);
        $this->routes[$route] = ['method' => 'POST'];
        return $this->add($route, $params);
    }

    /**
     * Add only PUT route to routing table.
     *
     * @param string $route  The route URL
     * @param array  $params Parameters (controller, action, etc.)
     *
     * @return void|null
     */
    public function put($route, $params = []){
        $route = str_replace('/', '', $route);
        $this->routes[$route] = ['method' => 'PUT'];
        return $this->add($route, $params);
    }

    /**
     * Add only DELETE route to routing table.
     *
     * @param string $route  The route URL
     * @param array  $params Parameters (controller, action, etc.)
     *
     * @return void
     */
    public function delete($route, $params = []){
        $route = str_replace('/', '', $route);
        $this->routes[$route] = ['method' => 'DELETE'];
        return $this->add($route, $params);
    }

    /**
     * Get all the routes from the routing table
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Match the route to the routes in the routing table, setting the $params
     * property if a route is found.
     *
     * @param string $url The route URL
     *
     * @return boolean  true if a match found, false otherwise
     */
    public function match($url)
    {
        foreach ($this->routes as $route => $params) {
            if ($route == $url) {
                $this->params = $params;
                return true;
            }
        }

        return false;
    }

    /**
     * Get the currently matched parameters
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Run the route, creating the controller object and running the
     * action method
     *
     * @param string $url The route URL
     *
     * @return void
     */
    public function run($url)
    {
        $url = str_replace('/', '', $url);
        $url = $this->removeQueryStringVariables($url);

        if ($this->match($url)) {
            $controller = $this->routes[$url]['controllerName'];

            if ($this->routes[$url]['method'] != $_SERVER['REQUEST_METHOD']){
                http_response_code(405);
                    echo json_encode([
                    'status' => false,
                    'message' => 'Method not allowed.'
                ]);
                die();
            }

            if (class_exists($controller)) {
                $controllerObject = new $controller(
                    $this->routes[$url]['data'],
                    $this->routes[$url]['action']
                );
                $action = $this->routes[$url]['action'];
                if (method_exists($controller, $action)){
                    $controllerObject->middleware($action);
                    $controllerObject->$action();
                } else {
                    http_response_code(404);
                    echo json_encode([
                        'status' => false,
                        'message' => 'Controller method not found.'
                    ]);
                    die();
                }

            } else {
                http_response_code(404);
                echo json_encode([
                    'status' => false,
                    'message' => 'Controller class $controller not found.'
                ]);
                die();
            }
        } else {
            http_response_code(404);
            echo json_encode([
                'status' => false,
                'message' => 'No route matched.'
            ]);
            die();
        }
    }

    /**
     * Convert the string with hyphens to StudlyCaps,
     * e.g. post-authors => PostAuthors
     *
     * @param string $string The string to convert
     *
     * @return string
     */
    protected function convertToStudlyCaps($string)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    /**
     * Remove the query string variables from the URL (if any). As the full
     * query string is used for the route, any variables at the end will need
     * to be removed before the route is matched to the routing table. For
     * example:
     *
     *   URL                           $_SERVER['QUERY_STRING']  Route
     *   -------------------------------------------------------------------
     *   localhost                     ''                        ''
     *   localhost/?                   ''                        ''
     *   localhost/?page=1             page=1                    ''
     *   localhost/posts?page=1        posts&page=1              posts
     *   localhost/posts/index         posts/index               posts/index
     *   localhost/posts/index?page=1  posts/index&page=1        posts/index
     *
     * A URL of the format localhost/?page (one variable name, no value) won't
     * work however. (NB. The .htaccess file converts the first ? to a & when
     * it's passed through to the $_SERVER variable).
     *
     * @param string $url The full URL
     *
     * @return string The URL with the query string variables removed
     */
    protected function removeQueryStringVariables($url)
    {
        if ($url != '') {
            $parts = explode('&', $url, 2);

            if (strpos($parts[0], '=') === false) {
                $url = $parts[0];
            } else {
                $url = '';
            }
        }

        return $url;
    }

    /**
     * Get the namespace for the controller class. The namespace defined in the
     * route parameters is added if present.
     *
     * @return string The request URL
     */
    protected function getNamespace()
    {
        $namespace = 'App\Controllers\\';

        if (array_key_exists('namespace', $this->params)) {
            $namespace .= $this->params['namespace'] . '\\';
        }

        return $namespace;
    }
}