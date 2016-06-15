<?php
/** $Id$
 * EventDispatcherInterface.php
 *
 * @version 1.0.0, $Revision$
 * @package Events
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2016, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Events;
use Core\Events\Model\EventModelInterface as EventModel;


interface EventDispatcherInterface {

    public function publish($channel, $message);

}