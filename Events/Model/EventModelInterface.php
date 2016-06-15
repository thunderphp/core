<?php
/** $Id$
 * EventModelInterface.php
 *
 * @version 1.0.0, $Revision$
 * @package Events\Model
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2016, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Events\Model;
use \Core\Events\EventDispatcherInterface as EventDispatcher;

interface EventModelInterface {

    public function dispatch(EventDispatcher &$dispatcher);

}