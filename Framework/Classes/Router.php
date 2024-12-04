<?php


namespace RTAQI\Framework\Classes;


use Exception;
use League\Plates\Engine;
use RTAQI\Controllers\Error\PageNotFoundController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Router
{
    private $routes = array();
    private $request;
    private $viewEngine;

    public function __construct(Request $request, Engine $viewEngine = null)
    {
        $this->request = $request;
        $this->viewEngine = $viewEngine;
    }

    /**
     * @throws Exception
     */
    public function addRoute(string $controller, array $paths)
    {
        foreach ($paths as $path) {
            if ($this->routeExists($path)) {
                throw new Exception($path . " already defined.");
            }

            if (!class_exists($controller)) {
                throw new Exception($controller . " class not found.");
            }

            $this->routes[] = [
                "path" => $path,
                "controller" => $controller
            ];
        }

    }

    private function routeExists($path): bool
    {
        return !($this->getRouteIndex($path) == -1);
    }

    private function getRouteIndex(string $path): int
    {

        foreach ($this->routes as $index => $route) {
            if ($route["path"] == $path)
                return $index;
        }
        return -1;
    }

    public function respond(array $params = []): Response
    {

        $ControllerClass = $this->getControllerForPath($this->request->getPathInfo());

        $controller = new $ControllerClass();

        return $controller->handler($this->request, $this->viewEngine, $params);

    }

    private function getControllerForPath(string $path)
    {

        $routeIndex = $this->getRouteIndex($path);

        if ($routeIndex == -1) {
            return PageNotFoundController::class;
        }

        return $this->routes[$routeIndex]["controller"];
    }

}