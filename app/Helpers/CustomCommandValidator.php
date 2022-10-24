<?php
namespace App\Helpers;

use App\Helpers\CommandHelper;

class CustomCommandValidator
{
    public static function validateXmlSaveCommandOptions($options)
    {
        if (!$options['path']) {
            PrintConsole::error("[path] option is required");
            return false;
        }
        
        if ($options['save'] && !$options['medium']) {
            PrintConsole::error("[medium] option is required, e.g --medium=mysql or --medium=spreadsheet");
            return false;
        }

        if ($options['save'] && $options['medium'] === 'spreadsheet') {
            // Validating options in case of spreadsheet
            $requiredOptions = [
                'cfn' => '--cfn=configurationFilename.json',
                'sid' => '--sid=spreadsheetId',
                'sn' => '--sn=sheetname'
            ];
            
            $isOptionsError = false;
            $optionsError = '';
            
            foreach($requiredOptions as $option => $msg) {
                if (empty($options[$option]) || is_null($options[$option])) {
                    $isOptionsError = true;
                    $optionsError .= " {$msg},";
                }
            }
            
            $optionsError = trim($optionsError,',');
            // config/credentials file name is missing 
            if( $isOptionsError) {
                PrintConsole::error("[Error] to save data, please provide: ".$optionsError . ' option(s)');
                return false;
            }
        }

        $path = CommandHelper::getFilePath($options['path']);

        // File must exist before we proceed
         if(!\Storage::exists($path)) {
            PrintConsole::error("File [{$path}] does not exists!");
            return false; // Error exit code
        }

        // File does exists: but check for options, validate and save
        if(!$options['validate'] && !$options['save']) {
            PrintConsole::warning("File [{$path}] does exists!, Please provide some more options what to do with this file?");
            return false;
        }
        
        /** 
         * If you want to save the result to google sheet and
         * did not profived config filename, or 
         * the config file does not exists
         */
        if(!\Storage::exists('/google/'.$options['cfn']) && $options['save']) {
            // Credentials file does not exists
            PrintConsole::error("Config file [{$options['cfn']}] does not exists!");
            return false;
        }

        return true;
    }
}


?>