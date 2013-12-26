<?php
/**
 * CsvFileReader.php
 *
 * @copyright (c) 2013, Sierra Bravo Corp., dba The Nerdery, All rights reserved
 * @license BSD-2-Clause
 */
namespace Nerdery\CsvBundle\FileReader;

use Nerdery\CsvBundle\Event\CsvParseErrorEvent;
use Nerdery\CsvBundle\Exception\FileInvalidException;
use Nerdery\CsvBundle\Exception\NoHeaderForDataColumnException;
use Nerdery\CsvBundle\FileReader\CsvFileReaderInterface;
use Nerdery\CsvBundle\FileReader\Options\CsvFileReaderOptions;
use Nerdery\CsvBundle\FileReader\Options\CsvFileReaderOptionsInterface;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * CsvFileReader
 *
 * @author Thomas Houfek <thomas.houfek@nerdery.com>
 */
class CsvFileReader implements CsvFileReaderInterface
{
    /**
     * Options object.
     *
     * @var CsvFileReaderOptionsInterface
     */
    private $options;

    /**
     * Event Dispatcher
     *
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

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

    /**
     * Waiting For Header flag.
     *
     * @var bool
     */
    private $waitingForHeader;

    /**
     * Current Line Number
     *
     * @var int
     */
    private $currentLineNumber;

    /**
     * Constructor.
     *
     * @param CsvFileReaderOptionsInterface $options
     */
    public function __construct(
        CsvFileReaderOptionsInterface $options,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->options           = $options;
        $this->eventDispatcher   = $eventDispatcher;
        $this->waitingForHeader  = $this->options->isHeaderExpected();
        $this->currentLineNumber = 0;
    }

    /**
     * Open the file.
     *
     * @param string $path
     * @return void
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

        return;
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
    public function parse($path)
    {
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
            $data = $this->getRowData();
        } catch (\Exception $e) {

            $event = new CsvParseErrorEvent($e, $this->getCurrentLineNumber());
            $this->eventDispatcher->dispatch(
                CsvParseErrorEvent::EVENT_KEY,
                $event
            );

        }
        return $data;
    }

    /**
     * GetRowData.
     *
     * Returns an array of row data.
     *
     * Under the 'correspondingDataOptional' and 'correspondingDataRequired'
     * header policies, this will be an associative array, where the keys are
     * the labels in the CSV header row, and the values are the corresponding
     * column data for a row in the CSV file.
     *
     * @return array|null
     * @throws NoHeaderForDataColumnException If a column has no corresponding
     *     header label.
     */
    public function getRowData()
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

        if (CsvFileReaderOptions::HEADER_POLICY_SUB_DATA_OPTIONAL ==
            $this->options->getHeaderPolicyOption()
        ) {
            $this->assertNoMoreDataThanLabels(
                $rowValuesArray
            );
            $rowValuesArray = $this->supplyMissingValuesForTrailingLabels(
                $rowValuesArray
            );
        }

        if (CsvFileReaderOptions::HEADER_POLICY_SUB_DATA_REQUIRED ==
            $this->options->getHeaderPolicyOption()
        ) {
            $this->assertDataForAllLabels(
                $rowValuesArray
            );
        }

        $data = $this->applyDataHandlingOptions($rowValuesArray);

        return $data;
    }

    /**
     * Parse the CSV file header.
     */
    public function parseHeader()
    {
        $headerArray= $this->convertRowToValuesArray();

        if (true == $this->options->useLabelsAsKeys()) {
            $this->createLabelsArray($headerArray);
        }
    }



    protected function assertNoMoreDataThanLabels($rowValuesArray)
    {
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
    protected function assertDataForAllLabels($rowValuesArray)
    {
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

    /**
     * Supply missing values for trailing labels.
     *
     * If the 'enforceDataForAllHeaders' option is set to false, it is
     * acceptable for the number of label elements in the header row
     * to be greater than the number of values in a given data row.
     *
     * In this case we 'pad' the values in the data row with nulls, so
     * that when we call array_combine, the length of the row data values
     * array is the same as the length of the labels array.

     * The converse is NOT true: the number of values in the data row
     * may not be greater than the number of elements in the header --
     * as this would make it impossible to retrieve a data value using the
     * header value as a key.  So in this case an exception is thrown.
     *
     * @param $rowValuesArray
     * @return mixed
     */
    public function supplyMissingValuesForTrailingLabels($rowValuesArray)
    {

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
            $this->options->getLengthOption(),
            $this->options->getDelimiterOption(),
            $this->options->getEnclosureOption(),
            $this->options->getEscapeOption()
        );
        return $valuesArray;
    }

    /**
     * Create the Labels array
     *
     * @paam array $headerArray
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
     * @param array $rowValuesArray
     * @param int $numNullsToPush
     * @return mixed
     */
    private function padRowValuesArray($rowValuesArray, $numNullsToPush)
    {
        for ($i = 1; $i <= $numNullsToPush; $i++) {
            array_push($rowValuesArray, null);
        }
        return $rowValuesArray;
    }

    /**
     * Apply data handling options.
     *
     * Determines, according to the Options, whether each row of file data is
     * represented by an associative array keyed to the file header labels, or
     * whether it is represented by a one-dimensional array of values.
     *
     * @param array $rowValuesArray
     * @return array
     */
    private function applyDataHandlingOptions($rowValuesArray)
    {
        $data = null;
        if (true === $this->options->useLabelsAsKeys()) {
            // Here we are merging the labels array and the row values array
            // so that we can retrieve values in the latter using keys supplied by
            // the former.
            $data = array_combine(
                $this->labelsArray,
                $rowValuesArray
            );
            return $data;
        } else {
            $data = $rowValuesArray;
            return $data;
        }
    }
}
