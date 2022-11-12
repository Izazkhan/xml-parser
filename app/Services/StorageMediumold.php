<?php
namespace App\Services;

use App\Helpers\PrintConsole;
use App\Interfaces\StorageMediumInterface;

class StorageMedium
{
    protected $service;
    
    public function __construct(StorageMediumInterface $service)
    {
        $this->service = $service;
    }
    
    public function initialize()
    {
        $this->service->initialize();
    }
    
    public function connectAndSaveData($data)
    {
        $this->service->connectAndSaveData($data);
    }
}

?>