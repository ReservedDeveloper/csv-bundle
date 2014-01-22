<?php
/**
 * InvalidDataFieldException.php
 *
 * @package   Nerdery\CsvBundle\Exception
 * @copyright (c) 2013, Sierra Bravo Corp., dba The Nerdery, All rights reserved
 * @license   BSD-2-Clause
 */

namespace Nerdery\CsvBundle\Exception;

use \Exception;

/**
 * InvalidDataFieldException
 *
 * @package Nerdery\CsvBundle\Exception
 * @author  Daniel Lakes <dlakes@nerdery.com>
 */
class InvalidDataFieldException extends Exception
{
    /**
     * constructor
     *
     * @param string|int $fieldName  - the name of the field, or key if none
     * @param mixed      $fieldValue - the value provided for the field
     */
    public function __construct($fieldName = null, $fieldValue = null)
    {
        $fieldName = $fieldName
            ? " '$fieldName' "
            : " ";

        $fieldValue = $fieldValue
            ? " '$fieldValue' "
            : " ";

        $message = "The value" . $fieldValue . " for the specified field" .
                   $fieldName . "does not match the format dictated by the options";

        parent::__construct($message);
    }
}
