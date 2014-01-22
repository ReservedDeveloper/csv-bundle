<?php
/**
 * AbstractCsvFileParser
 *
 * @copyright (c) 2013, Sierra Bravo Corp., dba The Nerdery, All rights reserved
 * @license   BSD-2-Clause
 *
 * @package   Nerdery\CsvBundle\FileReader\Validator
 */

namespace Nerdery\CsvBundle\FileReader\Parser;

use Nerdery\CsvBundle\Exception\InvalidDataFieldException;
use Nerdery\CsvBundle\FileReader\Response\ParserResponse;

/**
 * Specifies base parser structure for use by the file reader
 *
 * @package Nerdery\CsvBundle\FileReader\Parser
 * @author  Daniel Lakes <dlakes@nerdery.com>
 * @version $Id$
 */
abstract class AbstractCsvFileParser
{
    /**
     * parses the data in the row to match the expected format.
     *
     * stores parsed response in response object
     *
     * @param array $rowData
     *
     * @return ParserResponse
     */
    public final function parseRow(array $rowData)
    {
        $response = new ParserResponse();
        foreach ($rowData as $dataFieldKey => &$dataFieldValue) {
            try {
                $dataFieldValue = $this->parseField($dataFieldKey, $dataFieldValue);
            } catch (\Exception $e) {
                $response->addErrorForField($dataFieldKey, $e);
            }
        }
        unset($dataFieldValue);

        $response->setParsedData($rowData);

        return $response;
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
     * @param mixed      $fieldValue
     *
     * @return mixed $parsedFieldValue
     * @throws InvalidDataFieldException
     */
    abstract public function parseField($fieldKey, $fieldValue);

    /**
     * parses a field as a DateTime type
     *
     * @param string $fieldName
     * @param string $fieldValue
     * @param string $formatStr
     *
     * @return \DateTime
     * @throws \Nerdery\CsvBundle\Exception\InvalidDataFieldException
     */
    public final function parseDatetimeFieldType($fieldName = "", $fieldValue, $formatStr)
    {
        $date = \DateTime::createFromFormat($formatStr, $fieldValue);

        if(!$date){
            throw new InvalidDataFieldException($fieldName, $fieldValue);
        }

        return $date;
    }
}
