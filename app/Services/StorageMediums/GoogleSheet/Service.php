<?php
namespace App\Services\StorageMediums\GoogleSheet;

use Google\Client;
use App\Helpers\PathHelper;
use App\Helpers\PrintConsole;
use App\Exceptions\ServiceException;
use Revolution\Google\Sheets\Sheets;
use PulkitJalan\Google\Facades\Google;
use App\Interfaces\StorageMedium;
use GuzzleHttp\Exception\ConnectException;
use League\Flysystem\FileNotFoundException;
use Google\Service\Exception as GoogleServiceException;

class Service implements StorageMedium
{
    protected $sheets;
    
    /**
    * Configuration of google sheet api.
    */
    public function __construct()
    {
        $task = "Google sheet";
        PrintConsole::start($task, 'Initializing...');
        $this->initialize();
        PrintConsole::completed($task . ' initialized');
    }
    
    public function initialize()
    {
        $path = PathHelper::getFilePath(config('google.service.file'));
        $config = config('google');
        $config = array_merge($config, [
            'service' => [
                'file'    => $path,
                'enable' => true
            ]
        ]);
        \Log::info('Entry 3: ' . $path);
        \Log::info(['Entry 4: ', $config]);
        
        $client = new \PulkitJalan\Google\Client($config);
        $googleClient = $client->getClient();
        $service = new \Google\Service\Sheets($googleClient);
        $sheets = new Sheets();
        $sheets->setService($service);
        $this->sheets = $sheets;
    }
    
    public function sheets()
    {
        return $this->sheets;
    }
    
    public function saveData($data)
    {
        try {
            $sheetList = $this->sheets()->spreadsheet(config('google.sheet.id'))->sheetList();
            // Check if sheet does exists
            if (in_array(config('google.sheet.name'), $sheetList)) {
                $sheet = $this->sheets()->spreadsheet(config('google.sheet.id'))->sheet(config('google.sheet.name'));
                $headers = $this->extractHeaders($data);
                $sheet->range('A1')->update([$headers]);
                $sheet->append($data['item']);
            } else {
                // Sheet does not exists
                throw new ServiceException("[SheetName] does not exists in spreadsheet, please provide a valid sheet name that does exists in spreadsheet");
            }
            
        } catch (GoogleServiceException $e) {
            \Log::error($e);
            if(@$e->getErrors()[0]['message']) {
                throw new ServiceException('Google Service Error: ' . $e->getErrors()[0]['message'] . ' Make sure both SpreadsheetId and SheetName are correct!');
            } else {
                throw new ServiceException('Google Service has reponded with an erorr, please check log file for more detial');
            }
            
        } catch (ConnectException $e) {
            \Log::error($e);
            throw new ServiceException("Connection failed, make sure you are connected to internet, for more detail please check log file");
        }
    }

    public function extractHeaders($catalog)
    {
        try {
            return array_keys($catalog['item'][0]);
        } catch (\Exception $e) {
            \Log::error($e);
            throw new ServiceException("Xml headers extraction failed, for more detail please check log file");
        }
    }
}
