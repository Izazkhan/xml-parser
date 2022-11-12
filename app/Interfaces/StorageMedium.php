<?php
namespace App\Interfaces;

interface StorageMedium
{
    public function initialize();
    public function saveData($data);
}


?>