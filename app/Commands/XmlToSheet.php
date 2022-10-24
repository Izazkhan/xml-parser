<?php

namespace App\Commands;

use Log;
use App\Helpers\PrintConsole;
use App\Helpers\CommandHelper;
use App\Services\StorageMedium;
use App\Services\XmlParser\Parser;
use App\Exceptions\ServiceException;
use App\Helpers\CustomCommandValidator;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use App\Services\StorageMediums\GoogleSheet\Service as GoogleSheetsService;

class XmlToSheet extends Command
{
    /**
    * The signature of the command.
    *
    * @var string
    */
    protected $signature = 'xml:save {--path=} {--medium=} {--cfn=} {--validate} {--eh} {--save} {--sn=} {--sid=}';
    
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
     * Execute the console command.
    *
    * @return mixed
    */
    public function handle()
    {
        $path = CommandHelper::getFilePath($this->option('path'));
        $errorExitCode = 0;
        $successExitCode = 1;
        try {
            if (!CustomCommandValidator::validateXmlSaveCommandOptions($this->options())) {
                return $errorExitCode;
            }
            
            // Get xml file path
            $path = CommandHelper::getFilePath($this->option('path'));
            
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
                    $dataToBeStored = [];
                    $sheetHeaders = [];
                }
                
                if ($this->option('save') === true) {
                    // Converting Xml to JSON
                    $task = "Converting Xml to JSON";
                    PrintConsole::start($task);
                    $catalog = $parser->xmlToArray($path);
                    $dataToBeStored = $catalog['item'];
                    PrintConsole::completed($task);
                    
                    if ($this->option('eh') === true) {
                        $task2 = "Extracting headers";
                        // Extracting headers
                        PrintConsole::start($task);
                        $sheetHeaders = $parser->extractHeaders($catalog);
                        PrintConsole::completed($task);
                    }
                }
                
            } catch (ServiceException $e) {
                PrintConsole::failed($task);
                PrintConsole::error($e->getMessage());
                return $errorExitCode; // Error exit code
            }
            
            // Google Spreadsheet section starts here
            $storageMedium = null;
            if ($this->option('save') === true) {
                if ($this->option('medium') == 'mysql') {
                    // Change storage Medium
                    // $storageMedium = new StorageMedium(new MysqlService($config));
                    PrintConsole::warning("No Implementation for MySql yet!");
                    return $errorExitCode; // Error exit code
                } else {
                    // Default: Spreadsheet
                    $configPath = CommandHelper::getFilePath($this->option('cfn'));
                    $storageMedium = new StorageMedium(
                        new GoogleSheetsService($configPath) // cfn: Google credentials/config file name
                    );
                }
                
                // This try_catch block is for Google Spreadsheet / Storage medium
                try {
                    $task = "Google spreadsheet";
                    // Task 1: to initialize google spreadsheet
                    PrintConsole::start($task, 'Initializing...');
                    $storageMedium->initialize();
                    PrintConsole::completed($task . ' initialized');
                    
                    // Task 2: to save data to google spreadsheet
                    PrintConsole::start($task, 'saving data...');
                    $storageMedium->connectAndSaveData([
                        'headers' => $sheetHeaders,
                        'data' => $dataToBeStored,
                        'spreadsheetId' => $this->option('sid'),
                        'sheetName' => $this->option('sn'),
                    ]);
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
