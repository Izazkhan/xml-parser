<?php
namespace App\Helpers;

/**
 * Helper class for command
 */
class PathHelper
{
    /**
     * @helper funtion
    */
    public static function getFilePath($path)
    {
        // removing staring slash from path
        if(strpos($path, '/') === 0) {
            $path = substr($path, 1);
        }
        
        // If this is a standalone app
        if(!\Phar::running()) {
            return $path;
        }
        
        $path = dirname(\Phar::running(false)).'/'.$path;
        return $path;
    }
}


?>
