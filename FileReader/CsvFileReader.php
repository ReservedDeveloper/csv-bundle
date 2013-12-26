<?php
/**
 * CsvFileReader.php
 *
 * @copyright (c) 2013, Sierra Bravo Corp., dba The Nerdery, All rights reserved
 * @license BSD-2-Clause
 */

namespace Nerdery\CsvBundle\FileReader;

use \InvalidArgumentException;
use Nerdery\CsvBundle\Exception\NoHeaderForDataColumnException;
use Nerdery\CsvBundle\Exception\FileInvalidException;
use Nerdery\CsvBundle\FileReader\CsvFileReaderInterface;

use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * CsvFileReader
 * @package Nerdery\CsvBundle\FileReader
 */
class CsvFileReader implements CsvFileReaderInterface {

    /**
     * File handle.
     *
     * @var resource
     */
    private $fileHandle;

    /**
     * Labels Array
     * 
     * @var array
     */
    private $labelsArray;

    private $waitingForHeader;

    /**
     * Current Line Number
     *
     * @var int
     */
    private $currentLineNumber;

    private $lengthOption;

    private $delimiterOption;

    private $enclosureOption;

    private $escapeOption;

    private $headerPolicyOption;

    private $useLabelsAsKeysOption;


    /**
     * Constructor.
     *
     * @param array $options
     * @throws InvalidArgumentException If given an unsupported option.
     * @throws FileException If file is not readable.
     */
    public function __construct($options = array())
    {
        $supportedOptions = [
            'length',
            'delimiter',
            'enclosure',
            'escape',
            'headerPolicy',
            'useLabelsAsKeys',
        ];

        foreach ($options as $option) {
            if (false === in_array($option, $supportedOptions)) {
                throw new InvalidArgumentException(
                    '"' . $option . '" is not a supported option.'
                );
            }
        }

        $this->length       = isset($options['length'])
                            ? $options['length']
                            : 0;

        $this->delimiter    = isset($options['delimiter'])
                            ? $options['delimiter']
                            : "\t";

        $this->enclosure    = isset($options['enclosure'])
                            ? $options['enclosure']
                            : '"';

        $this->escape       = isset($options['escape'])
                            ? $options['escape']
                            : '\\';

        $this->useLabelsAsKeys = isset($options['useLabelsAsKeys'])
            ? $options['useLabelsAsKeys']
            : true;

        $supportedHeaderPolicies = [
            'noHeader',
            'disregardHeader',
            'correspondingDataRequired',
            'correspondingDataOptional',
        ];

        if (isset($options['headerPolicy'])) {
            $headerPolicyOption = $options['headerPolicy'];
            if (false === in_array($headerPolicyOption, $supportedHeaderPolicies)) {
                throw new InvalidArgumentException(
                    '"' . $headerPolicyOption . '" is not a supported header ' .
                    'policy option.'
                );
            }
        }

        $this->headerPolicy = isset($options['headerPolicy'])
                            ? $options['headerPolicy']
                            : 'correspondingDataOptional';

        if ('noHeader' === $this->headerPolicy) {
            $this->waitingForHeader = false;
        } else {
            $this->waitingForHeader = true;
        }

        $this->currentLineNumber = 0;
    }

    /**
     * Open the file.
     *
     * @param string $path
     * @throws FileNotFoundException
     * @throws FileException
     */
    public function open($path)
    {
        if (false === file_exists($path)) {
            throw new FileNotFoundException($path);
        }

        if (false === is_readable($path)) {
            throw new FileException('Cannot read file: ' . $path);
        }

        $this->fileHandle = fopen($path, 'r');

        if (false === $this->fileHandle) {
            throw new FileException('Cannot open file: ' . $path);
        }
    }

    /**
     * Close the file.
     */
    public function close()
    {
        if ($this->fileHandle) {
            fclose($this->fileHandle);
        }
    }

    /**
     * Parse the file.
     */
    public function parse($path) {
        $this->open($path);

        $allRowsData = [];
        $endOfFile   = false;
        while (false === $endOfFile) {
            $data = $this->parseLine();

            if (false !== $data) {
                $allRowsData[] = $data;
            } else {
                $endOfFile = true;
            }
        }
        return $allRowsData;
    }

    /**
     * Parse a line of data.
     *
     * If data can be parsed it is returned as an array.  If instead parsing
     * throws an exception, we record the error and return false.
     *
     * @return array|false
     */
    private function parseLine()
    {
        $data = false;
        try {
            $data = $this->fileReader->getKeyedRowData();
            return $data;
        } catch (\Exception $e) {

            $this->errorReporter->addErrorForLine(
                $e->getMessage(),
                $this->fileReader->getCurrentLineNumber()
            );
            return $data;
        }
        return $data;
    }

    /**
     * GetKeyedRowData.
     *
     * Returns an associative array, where the keys are the labels in the CSV
     * header row, and the values are the corresponding column data for a row
     * in the CSV file.
     *
     * @return array|null
     * @throws NoHeaderForDataColumnException If a column has no corresponding
     *     header label.
     */
    public function getKeyedRowData()
    {
        // If we are expecting a header, parse the line as a header if we
        // have not already.
        if (true == $this->waitingForHeader) {
            $this->parseHeader();
            $this->waitingForHeader = false;
        }

        $rowValuesArray = $this->convertRowToValuesArray();

        // If $rowValuesArray is false we are presumably at the end of the file.
        if ($rowValuesArray === false) {
            return false;
        }

        if ('correspondingDataOptional' == $this->headerPolicy) {
            $this->assertNoMoreDataThanLabels(
                $rowValuesArray
            );
            $rowValuesArray = $this->supplyMissingValuesForTrailingLabels(
                $rowValuesArray
            );
        }

        if ('correspondingDataRequired' == $this->headerPolicy) {
            $this->assertDataForAllLabels(
                $rowValuesArray
            );
        }

        $data = null;
        if (true === $this->useLabelsAsKeys) {
            // Here we are merging the labels array and the row values array
            // so that we can retrieve values in the latter using keys supplied by
            // the former.
            $data = array_combine(
                $this->labelsArray,
                $rowValuesArray
            );
        } else {
            $data = $rowValuesArray;
        }

        return $data;
    }

    public function parseHeader()
    {
        $headerArray= $this->convertRowToValuesArray();
        if ('correspondingDataRequired' == $this->headerPolicy ||
            'correspondingDataOptional' == $this->headerPolicy
        ) {
            $this->createLabelsArray($headerArray);
        }
    }



    protected function assertNoMoreDataThanLabels($rowValuesArray) {
        // Under the 'correspondingDataOptional' header policy, it is
        // acceptable for the number of label elements in the header row
        // to be greater than the number of values in a given data row.
        //
        // The converse is NOT true: the number of values in the data row
        // may not be greater than the number of elements in the header --
        // as this would make it impossible to retrieve a data value using the
        // header value as a key.  So in this case an exception is thrown.
        $labelArrayLength = count($this->labelsArray);
        $valueArrayLength = count($rowValuesArray);
        $arrayLengthDiff  = $labelArrayLength - $valueArrayLength;
        if ($arrayLengthDiff < 0) {
            throw new NoHeaderForDataColumnException(
                $this->currentLineNumber,
                $labelArrayLength,
                $valueArrayLength
            );
        }
    }

    /**
     * @param $rowValuesArray
     * @throws NoHeaderForDataColumnException
     * @todo Make appropriate exception.
     */
    protected function assertDataForAllLabels($rowValuesArray) {
        // If the 'enforceDataForAllHeaders' option is set to false, it is
        // acceptable for the number of label elements in the header row
        // to be greater than the number of values in a given data row.
        //
        // In this case we 'pad' the values in the data row with nulls, so
        // that when we call array_combine, the length of the row data values
        // array is the same as the length of the labels array.
        //
        // The converse is NOT true: the number of values in the data row
        // may not be greater than the number of elements in the header --
        // as this would make it impossible to retrieve a data value using the
        // header value as a key.  So in this case an exception is thrown.
        $labelArrayLength = count($this->labelsArray);
        $valueArrayLength = count($rowValuesArray);
        $arrayLengthDiff  = $labelArrayLength - $valueArrayLength;
        if ($arrayLengthDiff != 0) {
            throw new NoHeaderForDataColumnException(
                $this->currentLineNumber,
                $labelArrayLength,
                $valueArrayLength
            );
        }
    }

    public function supplyMissingValuesForTrailingLabels($rowValuesArray) {
        // If the 'enforceDataForAllHeaders' option is set to false, it is
        // acceptable for the number of label elements in the header row
        // to be greater than the number of values in a given data row.
        //
        // In this case we 'pad' the values in the data row with nulls, so
        // that when we call array_combine, the length of the row data values
        // array is the same as the length of the labels array.
        //
        // The converse is NOT true: the number of values in the data row
        // may not be greater than the number of elements in the header --
        // as this would make it impossible to retrieve a data value using the
        // header value as a key.  So in this case an exception is thrown.
        $labelArrayLength = count($this->labelsArray);
        $valueArrayLength = count($rowValuesArray);
        $arrayLengthDiff  = $labelArrayLength - $valueArrayLength;

        $paddedRowValuesArray = $rowValuesArray;
        if ($arrayLengthDiff > 0) {
            $paddedRowValuesArray = $this->padRowValuesArray(
                $rowValuesArray,
                $arrayLengthDiff
            );
        }

        return $paddedRowValuesArray;
    }

    /**
     * Get current line number.
     *
     * @return int
     */
    public function getCurrentLineNumber()
    {
        return $this->currentLineNumber;
    }


    /**
     * Destructor.
     *
     * Ensures the file handle is closed when object goes out of scope.
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * ConvertRowToValuesArray
     *
     * Convert a row in the file to an array of values.
     *
     * @return array
     */
    private function convertRowToValuesArray()
    {
        $this->currentLineNumber += 1;
        $valuesArray = fgetcsv(
            $this->fileHandle,
            $this->length,
            $this->delimiter,
            $this->enclosure,
            $this->escape
        );
        return $valuesArray;
    }

    /**
     * Create the Labels array
     *
     * @throws FileInvalidException If Labels Array cannot be generated.
     */
    private function createLabelsArray(array $headerArray)
    {
        $this->labelsArray = $headerArray;
        if (false === $this->labelsArray) {
            throw new FileInvalidException();
        }
    }

    /**
     * Pad row values array.
     *
     * @param $rowValuesArray
     * @param $numNullsToPush
     * @return mixed
     */
    private function padRowValuesArray($rowValuesArray, $numNullsToPush) {
        for ($i = 1; $i <= $numNullsToPush; $i++) {
            array_push($rowValuesArray, null);
        }
        return $rowValuesArray;
    }
}