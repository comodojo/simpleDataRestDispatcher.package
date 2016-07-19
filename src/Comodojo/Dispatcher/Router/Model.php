<?php namespace Comodojo\Dispatcher\Router;

use \Comodojo\Dispatcher\Components\Model as DispatcherClassModel;
use \Comodojo\Dispatcher\Router\Table;
use \Comodojo\Dispatcher\Router\Route;
use \Comodojo\Dispatcher\Components\Timestamp as TimestampTrait;
use \Comodojo\Dispatcher\Request\Model as Request;
use \Comodojo\Dispatcher\Response\Model as Response;
use \Comodojo\Dispatcher\Extra\Model as Extra;
use \Comodojo\Dispatcher\Components\Configuration;
use \Comodojo\Cache\CacheManager;
use \Monolog\Logger;
use \Comodojo\Exception\DispatcherException;
use \Exception;

/**
 * @package     Comodojo Dispatcher
 * @author      Marco Giovinazzi <marco.giovinazzi@comodojo.org>
 * @author      Marco Castiello <marco.castiello@gmail.com>
 * @license     GPL-3.0+
 *
 * LICENSE:
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

class Model extends DispatcherClassModel {

    use TimestampTrait;

    private $bypass = false;
    
    private $route;

    private $cache;

    private $request;

    private $response;

    private $table;

    public function __construct(
        Configuration $configuration,
        Logger $logger,
        CacheManager $cache,
        Extra $extra
    ) {

        parent::__construct($configuration, $logger);

        $this->table = new Table($cache, $this);

        $this->cache = $cache;

        $this->extra = $extra;

        $this->setTimestamp();

    }

    public function table() {

        return $this->table;

    }

    public function bypass(Route $route) {

        $this->bypass = true;
        
        $this->route = $route;

        return $this;

    }

    public function route(Request $request) {

        $method = $request->method()->get();

        $methods = $this->configuration->get('allowed-http-methods');

        if ( ( $methods != null || !empty($methods) ) && in_array($method, $methods) === false ) {

            throw new DispatcherException("Method not allowed", 0, null, 405, array(
                "Allow" => implode(",",$methods)
            ));

        }

        $this->request = $request;

        if (!$this->bypass) {
            
            if (!$this->parse()) throw new DispatcherException("Unable to find a valid route for the specified uri", 0, null, 404);

        }
        
        return $this->route;

    }

    public function compose(Response $response) {

        $this->response = $response;
        
        if (is_null($this->route)) {
            
            throw new DispatcherException("Route has not been loaded!");
            
        }

        $service = $this->route->getInstance(
            $this->request,
            $this->response,
            $this->extra
        );

        if (!is_null($service)) {

            $result;

            $method = $this->request->method()->get();

            $methods = $service->getImplementedMethods();

            if ( in_array($method, $methods) ) {

                $callable = $service->getMethod($method);

                try {

                    $result = call_user_func(array($service, $callable));

                } catch (DispatcherException $de) {

                    throw new DispatcherException(sprintf("Service '%s' exception for method '%s': %s", $this->service, $method, $de->getMessage()), 0, $de, 500);

                } catch (Exception $e) {

                    throw new DispatcherException(sprintf("Service '%s' execution failed for method '%s': %s", $this->service, $method, $e->getMessage()), 0, $e, 500);

                }

            } else {

                throw new DispatcherException(sprintf("Service '%s' doesn't implement method '%s'", $this->service, $method), 0, null, 501, array(
                    "Allow" => implode(",", $methods)
                ));

            }

            $this->response->content()->set($result);

        } else {

            throw new DispatcherException(sprintf("Unable to execute service '%s'", $this->service), 0, null, 500);

        }

    }

    private function parse() {

        $path = $this->request->route();
        
        foreach ($this->table->routes() as $regex => $value) {
            
            // The current uri is checked against all the global regular expressions associated with the routes
            if (preg_match("/" . $regex . "/", $path, $matches)) {

                /* If a route is matched, all the bits of the route string are evalued in order to create
                 * new query parameters which will be available for the service class
                 */
                $this->route = $value->path($matches);

                return true;

            }

        }

        return false;

    }

}