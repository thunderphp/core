<?php

/** $Id$
 * SharedMemory.php
 *
 * @version 1.0.0, $Revision$
 * @package Core\Cache\Volatile
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2016, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Cache\Volatile;
    
use Core\Exceptions\MissingModuleException;


class SharedMemory {

    # The permissions that you wish to assign to your memory segment
    const MEMORY_MODE = 0777;

    # Instance of created class
    private static $instance = null;

    /** Return instance of SharedMemory class
     * @return \Core\Cache\Volatile\SharedMemory
     */
    public static function getInstance(){
        if(!self::$instance instanceof SharedMemory){
            self::$instance = new SharedMemory();
        }
        return self::$instance;
    }

    private function __construct() {
        if(!extension_loaded('shmop')){
            throw new MissingModuleException('shmop');
        }
    }

    /** Write data into shared memory block
     * @param $number
     * @param $value
     * @return bool|int
     */
    public function set($number, $value){

        # Delete shared memory block if any exists.
        $this->delete(intval($number));

        # Get data length.
        $size = strlen(strval($value));

        # Create or open shared memory block (sets IPC_CREATE flag).
        $mem = @shmop_open(intval($number), "c", self::MEMORY_MODE, $size);

        # Return FALSE if shmop_open failure.
        if($mem === false) return false;

        # Write data into shared memory block.
        $result = shmop_write($mem, strval($value), 0);

        # Close shared memory block.
        shmop_close($mem);

        # Return the size of the written data, or FALSE on failure.
        return $result;
    }

    /** Read data from shared memory block
     * @param $number
     * @return bool|string
     */
    public function get($number){

        # Open shared memory block for read only (sets SHM_RDONLY flag).
        $mem = @shmop_open(intval($number), "a", self::MEMORY_MODE, 0);

        # Return FALSE if shmop_open failure.
        if($mem === false) return false;

        # Get size of shared memory block.
        $size = shmop_size($mem);

        # Read data from shared memory block.
        $value = shmop_read($mem, 0, $size);

        # Close shared memory block.
        shmop_close($mem);

        # Return readed data.
        return $value;
    }

    /** Get size of shared memory block
     * @param $number
     * @return bool|int
     */
    public function size($number){

        # Open shared memory block for read only (sets SHM_RDONLY flag).
        $mem = @shmop_open(intval($number), "a", self::MEMORY_MODE, 0);

        # Return FALSE if shmop_open failure.
        if($mem === false) return false;

        # Get size of shared memory block.
        $size = shmop_size($mem);

        # Close shared memory block.
        shmop_close($mem);

        # Return data.
        return $size;
    }

    /** Delete shared memory block
     * @param $number
     * @return bool
     */
    public function delete($number){

        # Open shared memory block for read and write.
        $mem = @shmop_open(intval($number), "w", self::MEMORY_MODE, 0);

        # Return FALSE if shmop_open failure.
        if($mem === false) return false;

        # Delete shared memory block.
        $result = shmop_delete($mem);

        # Close shared memory block.
        shmop_close($mem);

        # Returns TRUE on success or FALSE on failure.
        return $result;
    }

}