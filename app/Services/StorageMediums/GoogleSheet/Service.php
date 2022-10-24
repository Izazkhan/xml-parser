<?php
namespace App\Services\StorageMediums\GoogleSheet;

use Google\Client;
use App\Exceptions\ServiceException;
use Revolution\Google\Sheets\Sheets;
use PulkitJalan\Google\Facades\Google;
use App\Interfaces\StorageMediumInterface;
use GuzzleHttp\Exception\ConnectException;
use League\Flysystem\FileNotFoundException;
use Google\Service\Exception as GoogleServiceException;

class Service implements StorageMediumInterface
{
    protected $sheets;
    protected $configFilename;
    /**
     * Configuration of google sheet api.
     */
    public function __construct($configFilename = '')
    {
       $this->configFilename = $configFilename;
    }

    public function initialize()
    {
        $config = config('google');
        if(is_string($this->configFilename) && $this->configFilename != '') {
            $config = array_merge($config, [
                'service' => [
                    'file'    => storage_path('google/'.$this->configFilename),
                    'enable'  => env('GOOGLE_SERVICE_ENABLED', true)
                ]
            ]);
        }
        
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
    
    public function connectAndSaveData($data)
    {
        try {
            $sheetList = $this->sheets()->spreadsheet($data['spreadsheetId'])->sheetList();
            // Check if sheet does exists
            if (in_array($data['sheetName'], $sheetList)) {
                $sheet = $this->sheets()->spreadsheet($data['spreadsheetId'])->sheet($data['sheetName']);
                if (!empty($data['headers'])) {
                    $sheet->range('A1')->update([$data['headers']]);
                }
                $sheet->append($data['data']);
            } else {
                // Sheet does not exists
                throw new ServiceException("[{$data['sheetName']}] does not exists in spreadsheet, please provide a valid sheet name that does exists in spreadsheet");
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
}
