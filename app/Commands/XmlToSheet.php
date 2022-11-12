<?php

namespace App\Commands;

use Log;
use App\Helpers\PrintConsole;
use App\Helpers\PathHelper;
use App\Interfaces\StorageMedium;
use App\Services\XmlParser\Parser;
use App\Exceptions\ServiceException;
use App\Helpers\CustomCommandValidator;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class XmlToSheet extends Command
{
    /**
    * The signature of the command.
    *
    * @var string
    */
    protected $signature = 'xml:save {--path=} {--validate} {--save}';
    
    /**
    * The description of the command.
    *
    * @var string
    */
    protected $description = 'Parse xml and then save data to Google Sheets';
    
    /**
    * to indicate error in command
    * @var bool
    */
    protected $isError = false;
    
    /**
    * Storage medium we will use to store data
    * @var bool
    */
    protected $medium;
    
    /**
     * Execute the console command.
    *
    * @return mixed
    */

    public function handle(StorageMedium $storageMedium)
    {
        $path = PathHelper::getFilePath($this->option('path'));
        $errorExitCode = 0;
        $successExitCode = 1;
        try {
            if (!CustomCommandValidator::validateXmlSaveCommandOptions($this->options())) {
                return $errorExitCode;
            }
            
            // Get xml file path
            $path = PathHelper::getFilePath($this->option('path'));
            
            // Xml Parser
            $parser = new Parser();
            
            // To indicate task heading on console
            $task = '';
            // This try_catch block is for Xml Parser
            try {
                $task = "Checking XML file";
                PrintConsole::start($task);
                $parser->loadFile($path);
                PrintConsole::completed($task);
                
                if($this->option('validate') === true) {
                    $task = "Validating XML";
                    // to make sure Xml conforms to the DTD rules
                    PrintConsole::start($task);
                    $parser->validateXml(); //validate xml
                    PrintConsole::completed($task);
                }
                
                if ($this->option('save') === true) {
                    // Converting Xml to JSON
                    $task = "Converting Xml to JSON";
                    PrintConsole::start($task);
                    $dataToBeStored = $parser->xmlToArray($path);
                     PrintConsole::completed($task);
                }
                
            } catch (ServiceException $e) {
                PrintConsole::failed($task);
                PrintConsole::error($e->getMessage());
                return $errorExitCode; // Error exit code
            }
            
            // Google Spreadsheet section starts here
            if ($this->option('save') === true) {
                
                // This try_catch block is for Google Spreadsheet / Storage medium
                try {
                    // Task: to save data to storage medium
                    PrintConsole::start($task, 'Saving data...');
                    $storageMedium->saveData($dataToBeStored);
                    PrintConsole::completed($task . ' data saved');
                    
                } catch (ServiceException $e) {
                    PrintConsole::failed($task);
                    PrintConsole::error($e->getMessage());
                    return $errorExitCode;
                }
                
            }
            // Google Spreadsheet section ends here
        } catch (\Exception $e) {
            \Log::error($e);
            $msg = "Something went wrong, Please check the log file for more detail";
            PrintConsole::error($msg);
            return $errorExitCode; // Error exit code
        }
        return $successExitCode; // Success ExitCode
    }
    
    /**
    * Define the command's schedule.
    *
    * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
    * @return void
    */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
