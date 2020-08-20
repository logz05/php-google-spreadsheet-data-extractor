<?php

namespace App\Adapters;

// Requires 'https://github.com/guzzle/guzzle'
use GuzzleHttp\Client;
// Requires 'https://laravel.com/api/5.8/Illuminate/Support/Arr.html'
use Illuminate\Support\Arr;

class GoogleSheetDataExtractor
{
	private $client;

    public function __construct()
    {
    	// Instantiate GuzzleHTTP object with no SSL verification and error reporting
    	$this->client = new Client(['verify' => false, 'http_errors' => false]);
    }

    public function getHTTPRequest(string $url) : ?object
    {
    	// Perform 'GET' request
    	$response = (new Client(['verify' => false, 'http_errors' => false]))->request('GET', $url);

    	// Return response if no error is encountered
    	return $response->getStatusCode() === 200 ? $response->getBody() : null;
    }

    public function getJSONArray(string $jsonString) : array
    {
    	// Search for the first '(' index encountered
    	$startIndex = strpos($jsonString,'(');
    	// Search for the last ')' index encountered
        $endIndex = strrpos($jsonString,')');

        // Extract out json string and parse into associative array
        return json_decode(substr($jsonString, $startIndex + 1, $endIndex - $startIndex - 1), true);
    }

    public function getRows(array $array, string $range) : ?array
    {
    	// Remove non-numeric characters and split by ':' (eg. 'A15:E28' -> ['15', '28'])
    	$formattedRange = explode(':', preg_replace("/[^0-9\:]/", "", $range));

    	// Make sure 'formattedRange' is an array of length 2
    	if(count($formattedRange) !== 2){
    		return null;
    	}

    	// Calculate number of rows
    	$numberOfRows = (int)$formattedRange[1] - (int)$formattedRange[0] + 1;

    	// Flatten response array
    	$flattenedArray = Arr::flatten($array['table']['rows']);

    	// Calculate no. of columns
    	$numberOfColumns = count($flattenedArray)/$numberOfRows;

    	// Make sure response array can be evenly divided into smaller arrays
    	if((int)$numberOfColumns !== $numberOfColumns){
    		return null;
    	}

    	// Evenly split array into smaller chunk and return it
    	return array_chunk($flattenedArray, $numberOfColumns);
    }

    public function getData(string $sheetId, string $sheetName, string $range, string $query) : ?array
    {
    	// Replace any unsafe characters
    	$query = urlencode($query);

    	// Send 'GET' request to Google Sheet and get response
    	$response = $this->getHTTPRequest("https://docs.google.com/spreadsheets/d/$sheetId/gviz/tq?sheet=$sheetName&range=$range&tq=$query");

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
