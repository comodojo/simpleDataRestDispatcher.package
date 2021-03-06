<?php namespace Comodojo\Dispatcher\Service;

use \Comodojo\Dispatcher\Components\AbstractModel;
use \Comodojo\Dispatcher\Request\Model as Request;
use \Comodojo\Dispatcher\Router\Model as Router;
use \Comodojo\Dispatcher\Response\Model as Response;
use \Comodojo\Dispatcher\Extra\Model as Extra;
use \Comodojo\Dispatcher\Traits\CacheTrait;
use \Comodojo\Dispatcher\Traits\RequestTrait;
use \Comodojo\Dispatcher\Traits\ResponseTrait;
use \Comodojo\Dispatcher\Traits\RouterTrait;
use \Comodojo\Dispatcher\Traits\ExtraTrait;
use \Comodojo\Foundation\Events\EventsTrait;
use \Comodojo\SimpleCache\Manager as CacheManager;
use \Comodojo\Foundation\Base\Configuration;
use \Comodojo\Foundation\Events\Manager as EventsManager;
use \Psr\Log\LoggerInterface;
use \Exception;

/**
 * @package     Comodojo Dispatcher
 * @author      Marco Giovinazzi <marco.giovinazzi@comodojo.org>
 * @author      Marco Castiello <marco.castiello@gmail.com>
 * @license     MIT
 *
 * LICENSE:
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

abstract class AbstractService extends AbstractModel {

    use CacheTrait;
    use EventsTrait;
    use RequestTrait;
    use RouterTrait;
    use ResponseTrait;
    use ExtraTrait;

    protected static $supported_methods = ['GET','PUT','POST','DELETE','OPTIONS','HEAD','TRACE','CONNECT','PURGE'];

    public function __construct(
        Configuration $configuration,
        LoggerInterface $logger,
        CacheManager $cache,
        EventsManager $events,
        Request $request,
        Router $router,
        Response $response,
        Extra $extra
    ) {

        parent::__construct($configuration, $logger);

        $this->setCache($cache);
        $this->setEvents($events);
        $this->setRequest($request);
        $this->setRouter($router);
        $this->setResponse($response);
        $this->setExtra($extra);

    }

    /**
     * Get service-implemented HTTP methods
     *
     * @return  array   Service implemented methods, in uppercase
     */
    public function getImplementedMethods() {

        $supported_methods = $this->getConfiguration()->get('supported-http-methods');

        if ( is_null($supported_methods) ) $supported_methods = self::$supported_methods;

        if ( method_exists($this, 'any') ) {

            return $supported_methods;

        }

        $implemented_methods = [];

        foreach ( $supported_methods as $method ) {

            if ( method_exists($this, strtolower($method)) ) array_push($implemented_methods, $method);

        }

        return $implemented_methods;

    }

    /**
     * Return the callable class method that reflect the requested one
     *
     */
    public function getMethod($method) {

        $method = strtolower($method);

        if ( method_exists($this, $method) ) {

            return $method;

        } else if ( method_exists($this, 'any') ) {

            return 'any';

        } else {

            return null;

        }

    }

}
