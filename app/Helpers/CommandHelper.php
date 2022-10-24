<?php
namespace App\Helpers;

/**
 * Helper class for command
 */
class CommandHelper
{
    /**
     * @helper funtion
    */
    public function getFilePath($path)
    {
        // removing staring slash from path
        if(strpos($path, '/') === 0) {
            $path = substr($path, 1);
        }
        
        // removing staring "storage" from path
        if(strpos($path, 'storage/') === 0) {
            $path = substr($path, 8);
        }
        return $path;
    }
}


?>