<?php
/**
 * AbstractCsvFileRowParser
 *
 * @copyright (c) 2013, Sierra Bravo Corp., dba The Nerdery, All rights reserved
 * @license   BSD-2-Clause
 *
 * @package   Nerdery\CsvBundle\FileReader\Parser
 */

namespace Nerdery\CsvBundle\FileReader\Parser;

use Nerdery\CsvBundle\FileReader\Response\ResponseHandler;

/**
 * Specifies base row parser structure for use by the file reader
 *
 * Any publicly accessible methods should operate on the row level
 * and should delegate out to protected functions.
 * Return value should always be bool false or the {@link getResponseData()} array
 *
 * Prior to calling a public method, resetResponse() should be called, passing in the
 * data set to parse
 *
 * Any methods containing parsing logic) (field/multi-field level)
 * should be at minimum protected and are responsible for
 * adding errors to the response object as appropriate
 * via addFieldErrorForResponse(). These functions are responsible for setting
 * the value of their respective fields. Values to parse should
 * thus be passed by reference to maintain consistency in parsing chains. Should return
 * bool to allow for chaining
 *
 * This allows consistent access to error messages, while delegating responsibility of
 * error handling to the logic-containing callee. Callers are then given the choice of how
 * to interpret these errors
 *
 * Error reporting on format issues should rely on generic error message. Errors
 * involving domain level logic should utilize {@link generateInvalidFieldMessage()}
 *
 * Public/protected approach is largely to facilitate the idea behind
 * a row parser, ensuring that row data is set before parsing begins
 *
 * @package Nerdery\CsvBundle\FileReader\Parser
 * @author  Daniel Lakes <dlakes@nerdery.com>
 * @version $Id$
 */
abstract class AbstractCsvFileRowParser extends ResponseHandler
{
    /**
     * validate non-header/data rows
     * Checks for validation on each field, then checks
     * for validation on multiple/joined fields
     *
     * Should be good for general purpose use.
     *
     * Extending methods should call parent, then check for !== false and pass
     * row data to additional callers
     *
     * @return array|bool parsed data on success, false on failure
     */
    public function parseRow()
    {
        $this->parseRowFields();

        return $this->getParsingResponse();
    }

    /**
     * parses each individual field in the data array for expected format.
     *
     * Setting as final to maintain consistency in terms of process/response.
     * Each field is always parsed and that row's data is subsequently updated.
     * The parsed data is then returned
     *
     * @return array|bool - an array of parsed data on success, false on failure.
     */
    public final function parseRowFields()
    {
        $rowData = $this->getResponseData();

        foreach ($rowData as $dataFieldKey => &$dataFieldValue) {
            $this->parseField($dataFieldKey, $dataFieldValue);
        }
        unset($dataFieldValue);

        $this->updateData($rowData);

        return $this->getParsingResponse();
    }

    /**
     * parses the given field for expected format
     *
     * On the implementing side, this will probably
     * look like a switch statement for each expected
     * field, until we can implement some
     * sort of field<==>data type mapping
     *
     * @param string|int $fieldKey
     * @param mixed      &$fieldValue - the value to parse, passed by reference
     *
     * @return bool
     */
    abstract protected function parseField($fieldKey, &$fieldValue);

    /**
     * parses a field as a DateTime type
     *
     * @param string $fieldName
     * @param string &$fieldValue - field value (by ref) to parse into date
     * @param string $formatStr - the date format str, matching format for {@link date()}
     *
     * @return bool
     */
    protected function parseDatetimeField($fieldName = "", &$fieldValue, $formatStr)
    {
        $date = \DateTime::createFromFormat($formatStr, $fieldValue);

        if(!$date){
            $this->addFieldErrorForResponse($fieldName, $fieldValue);

            return false;
        }

        $fieldValue = $date;

        return true;
    }

    /**
     * returns the response of a public row-parsing call
     *
     * @return array|bool
     */
    protected function getParsingResponse()
    {
        return $this->isResponseValid()
            ? $this->getResponseData()
            : $this->isResponseValid();
    }
}
