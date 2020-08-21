<?php

namespace App\Adapters;

use GuzzleHttp\Client;
use Illuminate\Support\Arr;

class GoogleSheetDataExtractor
{
	private $client;

    public function __construct()
    {
    	// Instantiate GuzzleHTTP object with no SSL verification and error reporting
    	$this->client = new Client(['verify' => false, 'http_errors' => false]);
    }

    public function getHTTPResponse(string $url) : ?string
    {
    	// Perform 'GET' request
    	$response = (new Client(['verify' => false, 'http_errors' => false]))->request('GET', $url);

    	// Return response if no error is encountered
    	return $response->getStatusCode() === 200 ? $response->getBody()->getContents() : null;
    }

    public function getJSONArray(string $jsonString) : array
    {
    	// Search for the first '(' index encountered
    	$startIndex = strpos($jsonString,'(');
    	// Search for the last ')' index encountered
        $endIndex = strrpos($jsonString,')');

        // Extract out json string and parse into associative array
        return json_decode(substr($jsonString, $startIndex + 1, $endIndex - $startIndex - 1), true) ?? [];
    }

    public function getRows(array $array, string $range) : ?array
    {
    	// Remove non-numeric characters and split by ':' (eg. 'A15:E28' -> ['15', '28'])
    	$formattedRange = explode(':', preg_replace("/[^0-9\:]/", "", $range));

    	// Make sure 'formattedRange' is an array of length 2
    	if(count($formattedRange) !== 2 || !count($array)){
    		return null;
    	}

    	// Calculate no. of columns
    	$numberOfColumns = count($array['table']['cols']);

        // Default value
        $rowsArray = [];

        // Extract data
        foreach($array['table']['rows'] as $element){
            foreach($element['c'] as $innerElement){
                array_push($rowsArray, $innerElement['v'] ?? null);
            }
        }

    	// Evenly split array into smaller chunk and return it
    	return array_chunk($rowsArray, $numberOfColumns);
    }

    public function getData(string $sheetId, string $sheetName, string $range, string $query) : ?array
    {
    	// Replace any unsafe characters
    	$query = urlencode($query);

    	// Send 'GET' request to Google Sheet and get response
    	$response = $this->getHTTPResponse("https://docs.google.com/spreadsheets/d/$sheetId/gviz/tq?sheet=$sheetName&range=$range&tq=$query");
       
    	// Make sure response exists
    	if(!$response){
    		return null;
    	}

    	// Parse to associative array
    	$array = $this->getJSONArray($response);

    	// Extract rows
    	$rowsData = $this->getRows($array, $range);

    	// Return rows data
    	return $rowsData;
    }
}
