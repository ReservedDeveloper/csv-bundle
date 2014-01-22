<?php
/**
 * InvalidHeaderFieldException.php
 *
 * @package   Nerdery\CsvBundle\Exception
 * @copyright (c) 2013, Sierra Bravo Corp., dba The Nerdery, All rights reserved
 * @license   BSD-2-Clause
 */

namespace Nerdery\CsvBundle\Exception;

use \Exception;

/**
 * InvalidHeaderFieldException
 *
 * @package Nerdery\CsvBundle\Exception
 * @author  Daniel Lakes <dlakes@nerdery.com>
 */
class InvalidHeaderFieldException extends Exception
{
    /**
     * constructor
     *
     * @param string|int|null $fieldName - message to display
     */
    public function __construct($fieldName = null)
    {
        $fieldName = $fieldName
            ? " '$fieldName' "
            : " ";

        $message = "The header field" . $fieldName . "does not match the format dictated by the options";

        parent::__construct($message);
    }
}
