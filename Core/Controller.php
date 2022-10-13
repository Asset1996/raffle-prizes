<?php

namespace Core;

/**
 * Base controller.
 */
abstract class Controller
{

    /**
     * Parameters from the matched route
     * @var array
     */
    protected $route_params = [];

    /**
     * Action of controller.
     * @var string
     */
    protected $route_action;

    /**
     * Data from request body.
     * @var any
     */
    protected $route_body;

    /**
     * Group of middlewares.
     * @var array
     */
    protected $middlewareGroup = [];

    /**
     * Class constructor
     *
     * @param array $route_params  Parameters from the route
     *
     * @return void
     */
    public function __construct(
        $route_body,
        $route_action
    )
    {
        $this->route_action = $route_action;
        $this->route_body = $route_body;
    }

    /**
     * Middleware handler.
     * @return void
     */
    public function middleware(string $action)
    {
        foreach ($this->middlewareGroup as $middleware => $methods){
            foreach ($methods as $method){
                if (method_exists($this, $method) && $action == $method){
                    $middlewareClass = '\App\Middlewares\\' . $middleware;
                    $middlewareClass::handle();
                }
            }
        }
    }
}