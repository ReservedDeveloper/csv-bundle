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
     * @param array $headerRow
     *
     * @return ValidatorResponse
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
     *
     * @param array $rowData - the array of row data to be checked
     *
     * @return ValidatorResponse
     */
    public function validateDataRow(array $rowData)
    {
        $response = new ValidatorResponse();
        foreach ($rowData as $dataFieldKey => $dataFieldValue) {
            if (!$this->validateDataField($dataFieldKey, $dataFieldValue)) {
                $response->addErrorForField($dataFieldKey, new InvalidDataFieldException($dataFieldKey, $dataFieldValue));
            }
        }

        return $response;
    }

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
