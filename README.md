# PHP Google Sheet Data Extractor

Extract Google Sheet data via QUERY link method *(without any Google Sheets API)*
<br>
###### (Requires PHP 7.X)

#### ![#00ff5e](https://via.placeholder.com/15/5e00ff/000000/?text=+) Make sure to set your sheet to be public viewable
<p align="center">
  <img src="https://i.imgur.com/lTLVet9.png" width="500" title="google sheet change view">
</p>

#### ![#00ff5e](https://via.placeholder.com/15/5e00ff/000000/?text=+)  Sample usage
```php
// Instantiate 'GoogleSheetDataExtractor' object
$googleSheetDataExtractor = new GoogleSheetDataExtractor();

// Extract data from sheet
$rows = $googleSheetDataExtractor->getData('<YOUR_SHEET_ID>', '<SHEET_NAME>', 'B5:D10', 'SELECT *');

// Display fetched per row data
var_dump($rows);
  ```
  
#### ![#00ff5e](https://via.placeholder.com/15/5e00ff/000000/?text=+)  Result
<p align="center">
  <img src="https://i.imgur.com/0Qp19Jk.png" width="750" title="CSS Calendar text">
  <img src="https://i.imgur.com/FjZiBcc.png" width="250" title="CSS Calendar text">
</p>

