<?php
/**
 * FileInvalidRowException.php
 *
 * @package Nerdery\CsvBundle\Exception
 * @copyright (c) 2013, Sierra Bravo Corp., dba The Nerdery, All rights reserved
 * @license BSD-2-Clause
 */

namespace Nerdery\CsvBundle\Exception;

use \Exception;

/**
 * InvalidDataFieldException
 *
 * @package Nerdery\CsvBundle\Exception
 * @author Daniel Lakes <dlakes@nerdery.com>
 */ 
class FileInvalidRowException extends Exception
{
    const IGNORE_LOGGING_CODE = 99999;
    const DEFAULT_EXCEPTION_MESSAGE = 'The row appears to be in an invalid format.';

    /**
     * @inheritdoc
     */
    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        $message = $message
            ? $message
            : self::DEFAULT_EXCEPTION_MESSAGE;

        parent::__construct($message, $code, $previous);
    }

}
