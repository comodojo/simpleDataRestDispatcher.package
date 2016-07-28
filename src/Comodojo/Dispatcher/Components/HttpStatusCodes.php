<?php namespace Comodojo\Dispatcher\Components;

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


class HttpStatusCodes {

    private $codes = array(
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        // Successful 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status', // missing
        208 => 'Already Reported', // missing
        226 => 'IM Used', // missing
        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized', // missing
        402 => 'Payment Required', // missing
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable', // missing
        407 => 'Proxy Authentication Required', // missing
        408 => 'Request Timeout', // missing
        409 => 'Conflict', // missing
        410 => 'Gone',
        411 => 'Length Required', // missing
        412 => 'Precondition Failed', // missing
        413 => 'Payload Too Large', // missing
        414 => 'URI Too Long', // missing
        415 => 'Unsupported Media Type', // missing
        416 => 'Range Not Satisfiable', // missing
        417 => 'Expectation Failed', // missing
        421 => 'Misdirected Request', // missing
        422 => 'Unprocessable Entity', // missing
        423 => 'Locked', // missing
        424 => 'Failed Dependency', // missing
        426 => 'Upgrade Required', // missing
        428 => 'Precondition Required', // missing
        429 => 'Too Many Requests', // missing
        431 => 'Request Header Fields Too Large', // missing
        451 => 'Unavailable For Legal Reasons', // missing
        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates (Experimental)', // missing
        507 => 'Insufficient Storage', // missing
        508 => 'Loop Detected', // missing
        510 => 'Not Extended', // missing
        511 => 'Network Authentication Required' // missing
    );
    
    public function exists($code) {
        
        return array_key_exists($code, $this->codes);
        
    }
    
    public function getMessage($code) {
        
        if ( $this->exists($code) ) return $this->code[$code];
        
        throw new Exception("Invalid HTTP status code $code");
        
    }
    
    public function isInformational($code) {
        
        return $code >= 100 && $code < 200;
        
    }
    
    public function isSuccessful($code) {
        
        return $code >= 200 && $code < 300;
        
    }
    
    public function isRedirection($code) {
        
        return $code >= 300 && $code < 400;
        
    }
    
    public function isClientError($code) {
        
        return $code >= 400 && $code < 500;
        
    }
    
    public function isServerError($code) {
        
        return $code >= 500 && $code < 600;
        
    }

}
