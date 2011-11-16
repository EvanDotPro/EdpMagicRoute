<?php

namespace EdpMagicRoute;

use Zend\EventManager\StaticEventManager;

class Module
{
    const ERROR_CONTROLLER_NOT_FOUND = 404;
    const ERROR_CONTROLLER_INVALID   = 404;
    const ERROR_EXCEPTION            = 500;

    public function init()
    {
        $events = StaticEventManager::getInstance();
        $events->attach('Zend\Mvc\Application', 'route', array($this, 'detectRouteLogic'), -100);
        $events->attach('Zend\Mvc\Application', 'dispatch', array($this, 'adjustRouteMatchForView'), -10);
    }

    public function getConfig()
    {
        return include __DIR__ . '/configs/module.config.php';
    }

    public function detectRouteLogic($e)
    {
        $app        = $e->getTarget();
        $locator    = $app->getLocator();
        $routeMatch = $e->getRouteMatch();
        $namespace  = $routeMatch->getParam('namespace', 'application');
        $controller = $routeMatch->getParam('controller', 'index');
        $action     = $routeMatch->getParam('action', 'index');

        try {
            // ZF2.0beta1 DI alias behavior 
            $controllerClass = $locator->get($namespace);
            $actionMethod = $this->segmentToAction($controller);

            if (!method_exists($controllerClass, $actionMethod)
                || !is_callable(array($controllerClass, $actionMethod))
            ) {
                throw new \Exception('Action not callable');
            }
            
            $routeMatch->setParam('controller', $namespace);
            $routeMatch->setParam('action', $controller);

        } catch (\Exception $exception) {

            try {
                // Check for matching namespace to first segment
                $namespaceClass = $this->segmentToClass($namespace);
                $controllerClass = $namespaceClass 
                    . '\\Controller\\' 
                    . $this->segmentToClass($controller) 
                    . 'Controller';

                $actionMethod = $this->segmentToAction($action);
                $reflection = new \ReflectionMethod($controllerClass, $actionMethod); 

                if (!method_exists($controllerClass, $actionMethod)
                    || !is_callable(array($controllerClass, $actionMethod))
                    || ($reflection->getDeclaringClass()->getName() !== $controllerClass)
                ) {
                    throw new \Exception('Action not callable');
                }

                $routeMatch->setParam('view-path', $controller);
                $routeMatch->setParam('controller', $controllerClass);

            } catch(\Exception $exception) {

                $namespaceClass = 'Application'; // @TODO: make this a setting
                $controllerClass = $namespaceClass 
                    . '\\Controller\\' 
                    . $this->segmentToClass($namespace) 
                    . 'Controller';

                $actionMethod = $this->segmentToAction($controller);

                if (!method_exists($controllerClass, $actionMethod)
                    || !is_callable(array($controllerClass, $actionMethod))
                ) {
                    $error = clone $e;
                    $error->setError(static::ERROR_CONTROLLER_NOT_FOUND);

                    $results = $app->events()->trigger('dispatch.error', $error);
                    if (count($results)) {
                        $return  = $results->last();
                    } else {
                        $return = $error->getParams();
                    }
                    return $return;
                }

                $routeMatch->setParam('view-path', $namespace);
                $routeMatch->setParam('controller', $controllerClass);
                $routeMatch->setParam('action', $controller);
            }
        }
    }

    public function adjustRouteMatchForView($e)
    {
        $routeMatch = $e->getRouteMatch();
        $routeMatch->setParam('controller', $routeMatch->getParam('view-path', $routeMatch->getParam('controller')));
    }
    
    protected function segmentToClass($segment)
    {
        // @TODO: Segment separators should be configurable
        $parts = explode('-', $segment);
        array_walk($parts, function (&$value) { $value = ucfirst($value); });
        return implode('', $parts);
    }

    protected function segmentToAction($segment)
    {
        return lcfirst($this->segmentToClass($segment)) . 'Action';
    }
}
