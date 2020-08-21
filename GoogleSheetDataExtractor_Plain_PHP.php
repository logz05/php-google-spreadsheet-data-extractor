<?php

class GoogleSheetDataExtractor
{
    public function __construct()
    {

    }

    public function getHTTPResponse(string $url) : ?string
    {
    	// Perform 'GET' request
    	$cURLConnection = curl_init();

		curl_setopt($cURLConnection, CURLOPT_URL, $url);
		curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($cURLConnection);

		// Check for errors
		if(curl_errno($cURLConnection) || curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE) !== 200 || curl_close($cURLConnection)){
			return null;
		}

    	// Return response
    	return $response;
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

    public function flattenedArray(array $array)
	{
	    return iterator_to_array(
	         new \RecursiveIteratorIterator(new \RecursiveArrayIterator($array))
	    , false);
	}
}
