<?php

/** $Id$
 * ObserverSubject.php
 *
 * @version 1.0.0, $Revision$
 * @package Core\Events\Observer
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2016, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Events\Observer;

use \SplSubject;

class ObserverSubject implements SplSubject {

    /** Observers
     * @var array
     */
    private $observers = array();

    /** Attachy new observer
     * @param \SplObserver $observer
     */
    public function attach(\SplObserver $observer){
        $this->observers[] = $observer;
    }

    /** Detach observer
     * @param \SplObserver $observer
     */
    public function detach(\SplObserver $observer){
        $index = array_search($observer, $this->observers);

        if (false !== $index) {
            unset($this->observers[$index]);
        }
    }

    public function notify(){
        /** @var SplObserver $observer */
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }


}