<?php
/**
 * AbstractCsvFileValidator
 *
 * @copyright (c) 2013, Sierra Bravo Corp., dba The Nerdery, All rights reserved
 * @license   BSD-2-Clause
 * @package   Nerdery\CsvBundle\FileReader\Validator
 */

namespace Nerdery\CsvBundle\FileReader\Validator;
use Nerdery\CsvBundle\Exception\InvalidDataFieldException;
use Nerdery\CsvBundle\Exception\InvalidHeaderFieldException;
use Nerdery\CsvBundle\FileReader\Response\ValidatorResponse;

/**
 * Specifies base validator structure for use by the file reader
 *
 * @package Nerdery\CsvBundle\FileReader\Validator
 * @author  Daniel Lakes <dlakes@nerdery.com>
 * @version $Id$
 */
abstract class AbstractCsvFileValidator
{
    /**
     * validate the header row
     *
     * as header rows do not have the same
     * nuance as data rows, decided to cast as
     * final to help ensure consistent return type
     *
     * @param array $headerRow
     *
     * @return ValidatorResponse $response
     */
    public final function validateHeader(array $headerRow)
    {
        $response = new ValidatorResponse();
        foreach ($headerRow as $headerFieldKey => $headerFieldValue) {
            if (!$this->validateHeaderField($headerFieldValue)) {
                $response->addErrorForField($headerFieldKey, new InvalidHeaderFieldException($headerFieldValue));
            }
        }

        return $response;
    }

    /**
     * validates the given header field for expected format
     *
     * @param string $fieldValue
     *
     * @return bool - true on success, false on failure
     */
    abstract protected function validateHeaderField($fieldValue);

    /**
     * validate non-header/data rows
     * Checks for validation on each field, then checks
     * for validation on multiple/joined fields
     *
     * Decided not to cast as final in the event that
     * a validation situation comes up not covered by one
     * of the below called methods
     *
     * As such, any classes extending this method should
     * pay close attention to the method return signature
     * for compatibility purposes
     *
     *
     *
     * @param array $rowData - the array of row data to be checked
     *
     * @return ValidatorResponse $response
     */
    public function validateDataRow(array $rowData)
    {
        $response = new ValidatorResponse();

        $response = $this->validateDataRowFields($rowData, $response);
        $response = $this->validateDataRowMultiFields($rowData, $response);

        return $response;
    }

    /**
     * validates each pair in the provided $rowData
     *
     * @param array             $rowData
     * @param ValidatorResponse $response - a preexisting response object to modify, if any
     * @return ValidatorResponse $response
     */
    protected final function validateDataRowFields(array $rowData, ValidatorResponse $response = null)
    {
        $response = $response
            ? $response
            : new ValidatorResponse();

        foreach ($rowData as $dataFieldKey => $dataFieldValue) {
            if (!$this->validateDataField($dataFieldKey, $dataFieldValue)) {
                $response->addErrorForField($dataFieldKey, new InvalidDataFieldException($dataFieldKey, $dataFieldValue));
            }
        }

        return $response;
    }

    /**
     * @param array             $rowData
     * @param ValidatorResponse $response
     *
     * @return ValidatorResponse $response
     */
    abstract protected function validateDataRowMultiFields(array $rowData, ValidatorResponse $response = null);

    /**
     * validates the given field for expected format
     *
     * On the implementing side, this will probably
     * look like a switch statement for each expected
     * field, until we can implement some
     * sort of field<==>data type mapping
     *
     * @param string|int $fieldKey
     * @param mixed      $fieldValue
     *
     * @return bool - true on success, false on failure
     */
    abstract protected function validateDataField($fieldKey, $fieldValue);

    /**
     * Given a field value validate
     * that the specified field matches
     * the date format specified
     *
     * @param string $dateFieldValue
     * @param string $dateFormat
     *
     * @return bool true on success, false on failure
     */
    protected final function validateExpectedFieldDateFormat($dateFieldValue, $dateFormat)
    {
        if (empty($dateFieldValue)) {
            return true;
        }

        $date = \DateTime::createFromFormat($dateFormat, $dateFieldValue);

        if (!$date) {
            return false;
        }

        return true;
    }
}
