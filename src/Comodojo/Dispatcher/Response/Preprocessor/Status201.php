<?php namespace Comodojo\Dispatcher\Response\Preprocessor;

use \Comodojo\Dispatcher\Response\Model as Response;

/**
 * Status: Created
 *
 * @package     Comodojo Dispatcher
 * @author      Marco Giovinazzi <marco.giovinazzi@comodojo.org>
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

class Status201 extends Status200 {

    /**
     * {@inheritdoc}
     */
    public function consolidate(Response $response) {

        $location = $response->getLocation()->get();

        if ($location != null) {
            $response->getHeaders()
                ->set("Location", $location);
        }

        parent::consolidate();

    }

}
