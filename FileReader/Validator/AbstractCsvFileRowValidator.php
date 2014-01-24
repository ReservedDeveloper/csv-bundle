<?php
/**
 * AbstractCsvFileRowValidator
 *
 * @copyright (c) 2013, Sierra Bravo Corp., dba The Nerdery, All rights reserved
 * @license   BSD-2-Clause
 * @package   Nerdery\CsvBundle\FileReader\Validator
 */

namespace Nerdery\CsvBundle\FileReader\Validator;
use Nerdery\CsvBundle\FileReader\Response\ResponseHandler;

/**
 * Specifies base row validator structure for use by the file reader
 *
 * In general, any validation methods should return bool pass|fail
 *
 * Any publicly accessible methods should operate on the row level
 * and should delegate out to protected functions.
 *
 * Prior to calling a public method, resetResponse() should be called, passing in the
 * data set to validate
 *
 * Field-level validation (or any methods containing validation logic)
 * should be at minimum protected and is responsible for
 * adding errors to the response object as appropriate
 * via addFieldErrorForResponse()
 *
 * This allows consistent access to error messages, while delegating responsibility of
 * error handling to the logic-containing callee. Callers are then given the choice of how
 * to interpret these errors
 *
 * Any validation chaining should always go from general to specific, looking first for
 * expected format/type, then at the particular contents.
 *
 * Error reporting on format issues should rely on generic error message. Errors
 * involving domain level logic whcih need to insert the field name and key
 * should utilize {@link generateInvalidFieldMessage()}
 *
 * Public/protected approach is largely to facilitate the idea behind
 * a row validator, ensuring that row data is set before validation begins
 *
 * @package Nerdery\CsvBundle\FileReader\Validator
 * @author  Daniel Lakes <dlakes@nerdery.com>
 * @version $Id$
 */
abstract class AbstractCsvFileRowValidator extends ResponseHandler
{
    /**
     * validate a previously set header row
     *
     * @return bool true on success, false on failure
     */
    public function validateHeader()
    {
        $this->validateEachHeaderField();

        return $this->isResponseValid();
    }

    /**
     * iterates through each field in the response object data,
     * examining as a header field
     *
     * @return bool pass|fail
     */
    public final function validateEachHeaderField(){
        foreach ($this->getResponseData() as $headerFieldValue) {
            $this->validateHeaderField($headerFieldValue);
        }

        return $this->isResponseValid();
    }

    /**
     * validates the given header field for expected format
     * Adds error messages to the response as appropriate
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
     * Should be good for general purpose use.
     * Example reasons for extending might
     * include to add multi-field validation
     *
     * @return bool true on success, false on failure
     */
    public function validateDataRow()
    {
        $this->validateEachDataField();

        return $this->isResponseValid();
    }

    /**
     * validates each pair in the data stored in our response
     *
     * @return bool
     */
    public final function validateEachDataField()
    {
        foreach ($this->getResponseData() as $dataFieldKey => $dataFieldValue) {
            $this->validateDataField($dataFieldKey, $dataFieldValue);
        }

        return $this->isResponseValid();
    }

    /**
     * validates the given field for expected format
     *
     * On the implementing side, this will probably
     * look like a switch statement for each expected
     * field, until we can implement some
     * sort of field<==>data type mapping
     *
     * Adds error messages to the response as appropriate
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
     * @param string $dateFieldKey
     * @param string $dateFieldValue
     * @param string $dateFormat
     *
     * @return bool true on success, false on failure or empty $dateFieldValue
     */
    protected final function validateExpectedFieldDateFormat($dateFieldKey, $dateFieldValue, $dateFormat)
    {
        $date = \DateTime::createFromFormat($dateFormat, $dateFieldValue);

        if (!$date) {
            $this->addFieldErrorForResponse($dateFieldKey, $dateFieldValue);
            return false;
        }

        return true;
    }
}
