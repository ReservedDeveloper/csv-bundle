#Nerdery CSV
-------------
This is a bundle used for the reading, validation, and parsing of TSV & CSV files developed internally at [The Nerdery](http://www.nerdery.com).

##Installation (via Composer)
=
Drop the following into your project's composer.json require block:
   >`"nerdery/csv-bundle": "v0.0.3"`

##Basic Usage
```php
$readerOptions = array(
     CsvFileReaderOptions::OPTION_VALIDATION => $this->csvFileValidator,
     CsvFileReaderOptions::OPTION_PARSER     => $this->csvFileParser,
);

$csvFileReaderOptions = new CsvFileReaderOptions($readerOptions);

$reader =  new CsvFileReader(
    $csvFileReaderOptions,
    $this->eventDispatcher
);

$dataRows = $reader->parse(PATH_TO_FILE);

//Do some stuff with the data
```
##Classes

##[`CsvFileReaderOptions`](./FileReader/Options/CsvFileReaderOptions)

This class handles configuring the file reader for usage, setting things like delimiter, adding parsing/validation, etc.

###Full options list:
- `CsvFileReaderOptions::OPTION_LENGTH`

 Used to specify the length of the longest line in the CSV file. Defaults to `0`, meaning no maximum is imposed.            
- `CsvFileReaderOptions::OPTION_DELIMITER`

 The character to be used for denoting the end of a column and beginning of the next. Defaults to `\t` (tab character).
- `CsvFileReaderOptions::OPTION_ENCLOSURE`

 The character that will be used for denoting a piece of text. Defaults to `"` (double quote).          
- `CsvFileReaderOptions::OPTION_ESCAPE`
The character used to escape reserved/special characters. Defaults to `\` (backslash).            
- `CsvFileReaderOptions::OPTION_HEADER_POLICY`

  The policy to designate how the header is going to be handled. Defaults to `CsvFileReaderOptions::HEADER_POLICY_SUB_DATA_OPTIONAL`
  ####Options:
      
    - `CsvFileReaderOptions::HEADER_POLICY_NO_HEADER` 
  
    Signifies that no header is present in the file being parsed
  
    - `CsvFileReaderOptions::HEADER_POLICY_DISREGARD`
    
    Signifies that a header is present, but should not be processed.    
  
    - `CsvFileReaderOptions::HEADER_POLICY_SUB_DATA_OPTIONAL`
    
    Process file with header. Signifies that the number of data columns *may* outnumber the number of header columns. Useful for variable column count files.
    - `CsvFileReaderOptions::HEADER_POLICY_SUB_DATA_REQUIRED`
    
    Process file with header. Signifies that the number of data columns in any given row **may not** exceed the number of columns in the header row.
          
- `CsvFileReaderOptions::OPTION_USE_LABELS_AS_KEYS`

  Whether or not to use header row labels as the keys for the array of row data. Defaults to `true`.

- `CsvFileReaderOptions::OPTION_VALIDATION`

 An implementation of the `AbstractCsvFileRowValidator` to use for validating the file. `null` by default.
  
- `CsvFileReaderOptions::OPTION_PARSER`
 
 An implementation of the `AbstractCsvFileRowParser` to use for parsing the file. `null` by default.
    
##[`CsvFileReader`](./FileReader/CsvFileReader)
The main operating class for the library. Handles running through the file, pushing it in to an
associative array (as well as calling any added parsing/validation). Takes `CsvFileReaderOptions` and
an instance of a class implementing `EventDispatcherInterface`.

##[`AbstractCsvFileRowValidator`](./blob/master/FileReader/Validator/AbstractCsvFileRowValidator)
Guidelines on usage to come. In the meantime, please see the class documentation.

##[`AbstractCsvFileRowParser`](./blob/master/FileReader/Parser/AbstractCsvFileRowParser)
Guidelines on usage to come. In the meantime, please see the class documentation.