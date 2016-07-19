<?php namespace Comodojo\Dispatcher\Router;

use \Comodojo\Dispatcher\Components\Model as DispatcherClassModel;
use \Monolog\Logger;
use \Comodojo\Dispatcher\Router\Model as Router;
use \Comodojo\Dispatcher\Router\Route;
use \Comodojo\Dispatcher\Components\Configuration;
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

class Parser extends DispatcherClassModel {
    
    private $router;

    public function __construct(
        Router $router
    ) {

        parent::__construct($router->configuration(), $router->logger());
        
        $this->router = $router;

    }
    
    // This method read the route (folder by folder recursively) and build 
    // the global regular expression against which all the request URI will be compared
    public function read($folders = array(), Route $value = null, $regex = '') {
        
        if (is_null($value)) {
            
            $value = new Route($this->router);
            
        }
        
        // if the first 'folder' is empty is removed
        while (!empty($folders) && empty($folders[0])) {

            array_shift($folders);

        }

        // if the 'folder' array is empty, the route has been fully analyzed
        // this is the exit condition from the recursive loop.
        if (empty($folders)) {

            return '^'.$regex.'[\/]?$';

        } else {

            // The first element of the array 'folders' is taken in order to be analyzed
            $folder  = array_shift($folders);
            
            // All the parameters of the route must be json strings
            $decoded = json_decode($folder, true);

            if (!is_null($decoded) && is_array($decoded)) {

                $param_regex    = '';

                $param_required = false;

                /* All the folders can include more than one parameter
                 * Eg: /service_name/{'param1': 'regex1', 'param2': 'regex2'}/
                 *     /calendar/{'ux_timestamp*': '\d{10}', 'microseconds': '\d{4}'}/
                 *
                 * The '*' at the end of the paramerter name implies that the parameter is required
                 * This example can be read as a calendar service that accepts both 
                 * timestamps in unix or javascript format.
                 *
                 * This is the reason of the following 'foreach'
                 */
                foreach ($decoded as $key => $string) {

                    $this->logger->debug("PARAMETER KEY: " . $key);

                    $this->logger->debug("PARAMETER STRING: " . $string);
                    
                    /* The key and the regex of every paramater is passed to the 'param'
                     * method which will build an appropriate regular expression and will understand 
                     * if the parameter is required and will build the Route query object
                     */
                    $param_regex .= $this->param($key, $string, $value);
                    
                    if ($value->isQueryRequired($key)) $param_required = true;

                    $this->logger->debug("PARAMETER REGEX: " . $param_regex);

                }
                // Once the parameter is analyzed, the result is passed to the next iteration
                return $this->read(
                    $folders,
                    $value,
                    $regex.'(?:\/'.$param_regex.')'. (($param_required)?'{1}':'?')
                );

            } else {
                // if the element is not a json string, I assume it's the service name
                $value->addService($folder);

                return $this->read(
                    $folders,
                    $value,
                    $regex.'\/'.$folder
                );

            }

        }

    }

    // This method read a single parameter and build the regular expression
    private function param($key, $string, $value) {

        $field_required = false;

        // If the field name ends with a '*', the parameter is considered as required
        if (preg_match('/^(.+)\*$/', $key, $bits)) {

            $key            = $bits[1];
            $field_required = true;

        }

        // The $value query object contains all regex which will be used by the collector to parse the route fields
        $value->setQuery($key, $string, $field_required);

        /* Every parameter can include it's own logic into the regular expression,
         * it can use backreferences and it's expected to be used against a single parameter.
         * This means that it can't be used as is to build the route regular expression,
         * Backreferences are not useful at this point and can make the regular expression more time consuming
         * and resource hungry. This is why they are replaced with the grouping parenthesis.
         * Eg: (value) changes in (?:value)
         *
         * Delimiting characters like '^' and '$' are also meaningless in the complete regular expression and
         * need to be removed. Contrariwise, wildcards must be delimited in order to keet the whole regular
         * expression consistent, hence a '?' is added to all the '.*' or '.+' that don't already have one.
         */
        $string = preg_replace("/(?<!\\\\)\\((?!\\?)/", '(?:', $string);
        $string = preg_replace("/\\.([\\*\\+])(?!\\?)/", '.${1}?', $string);
        $string = preg_replace("/^[\\^]/", '', $string);
        $string = preg_replace("/[\\$]$/", '', $string);

        /* The produced regular expression is grouped and associated with its key (this means that the 'preg_match'
         * function will generate an associative array where the key/value association is preserved).
         * If the field is required, the regular expression is completed with a '{1}' (which make it compulsory),
         * otherwise a '?' is added.
         */
        return '(?P<' . $key . '>' . $string . ')' . (($field_required)?'{1}':'?');

    }

}