<?php
namespace App\Services\XmlParser;

use Storage;
use DOMDocument;
use App\Exceptions\ServiceException;
use App\Services\XmlParser\Exceptions\InvalidXmlException;
use App\Services\XmlParser\Exceptions\XmlDTDValidationException;
use App\Services\XmlParser\Exceptions\XmlConversionFailedException;

class Parser
{
    protected $document;
    protected $path;
    /**
    * Initiate the parser
    */
    public function __construct()
    {
        $this->document = new \DOMDocument();
    }
    
    // To load .xml file
    public function loadFile($path)
    {
        try {
            
            $fileContents = file_get_contents($path);
            $this->document->loadXml($fileContents);

        } catch (\ErrorException $e) {
            // ErrorException get throew when document failed to load
            \Log::error($e);
            throw new ServiceException("File contains invalid Xml, for more detail please check log file");
        }
    }
    
    public function validateXml()
    {
        try {
            $this->document->validate();
        } catch (\Exception $e) {
            \Log::error($e);
            throw new ServiceException("Xml does not conforms to DTD, for more detail please check log file");
        }
    }
    
    public function xmlToArray($path)
    {
        try {
            $fileContents = file_get_contents($path);
            $data = simplexml_load_string($fileContents, 'SimpleXMLElement', LIBXML_NOCDATA);
            $json = json_encode($data);
            $json = str_replace("{}", "\"\"", $json);
            $catalog = json_decode($json,TRUE);
            return $catalog;
        } catch (\Exception $e) {
            \Log::error($e);
            throw new ServiceException("Xml to json conversion failed, for more detail please check log file");
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
?>