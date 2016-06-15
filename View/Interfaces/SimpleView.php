<?php
/** $Id$
 * SimpleView.php
 *
 * @version 1.0.0, $Revision$
 * @package Core\View\Interfaces
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2016, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\View\Interfaces;


interface SimpleView {

    public function prepareView();

    public function parseView();

    public function cleanView();

}