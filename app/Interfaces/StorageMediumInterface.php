<?php
namespace App\Interfaces;

interface StorageMediumInterface
{
    public function initialize();
    public function connectAndSaveData($data);
}


?>