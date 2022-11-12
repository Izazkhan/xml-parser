<?php
namespace App\Helpers;

use App\Helpers\PathHelper;

class CustomCommandValidator
{
    public static function validateXmlSaveCommandOptions($options)
    {
        if (!$options['path']) {
            PrintConsole::error("[path] option is required");
            return false;
        }

        $path = PathHelper::getFilePath($options['path']);

        // File must exist before we proceed
         if(!file_exists($path)) {
            PrintConsole::error("File [{$path}] does not exists!");
            return false; // Error exit code
        }

        // File does exists: but check for options, validate and save
        if(!$options['validate'] && !$options['save']) {
            PrintConsole::warning("File [{$path}] does exists!, Please provide some more options what to do with this file?");
            return false;
        }

        return true;
    }
}


?>