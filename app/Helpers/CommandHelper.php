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
        if(!\Phar::running()) {
            return $path;
        }
        $path = dirname(\Phar::running(false)).'/'.$path;
        // for docker container purpose: edge case that just happend
        if(strpos($path, '//') === 0) {
            $path = substr($path, 1);
        }
        return $path;
    }
}


?>