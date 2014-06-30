<?php namespace Mobishift\TemplateHelper;

use Illuminate\Routing\Router;

class HelperBuilder
{
    /**
     * Laravel application
     *
     * @var \Illuminate\Foundation\Application
     */
    public $app;

    /**
     * Create a new instance.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function routeController()
    {
        $routeArray = \Str::parseCallback($this->app->router->currentRouteAction(), null);

        if (last($routeArray) != null) {
            // Remove 'controller' from the controller name.
            return strtolower(str_replace('Controller', '', class_basename(head($routeArray))));
        }

        return 'unknownController';
    }

    public function routeAction()
    {
        $routeArray = \Str::parseCallback($this->app->router->currentRouteAction(), null);

        if (last($routeArray) != null) {
            // Take out the method from the action.
            return strtolower(str_replace(array('get', 'post', 'patch', 'put', 'delete'), '', last($routeArray)));
        }

        return 'unknownAction';	
    }
}