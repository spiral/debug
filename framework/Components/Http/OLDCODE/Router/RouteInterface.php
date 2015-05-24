<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 * @copyright ©2009-2015
 */
namespace Spiral\Components\Http\Router;

use Psr\Http\Message\ServerRequestInterface;
use Spiral\Core\CoreInterface;

interface RouteInterface
{
    /**
     * Get route name. Name is requires to correctly identify route inside router stack (to generate
     * url for example).
     *
     * @return string
     */
    public function getName();

    /**
     * Check if route matched with provided request. Will check url pattern and pre-conditions.
     *
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function match(ServerRequestInterface $request);

    /**
     * Perform route on given Request and return response.
     *
     * @param ServerRequestInterface $request
     * @param CoreInterface          $core
     * @param array                  $middlewares Middleware aliases provided from parent router.
     * @return mixed
     */
    public function perform(
        ServerRequestInterface $request,
        CoreInterface $core,
        array $middlewares = array()
    );

    /**
     * Create URL using route parameters (will be merged with default values), route pattern and base
     * path.
     *
     * @param array  $parameters
     * @param string $basePath
     * @return string
     */
    public function buildURL(array $parameters = array(), $basePath = '/');
}