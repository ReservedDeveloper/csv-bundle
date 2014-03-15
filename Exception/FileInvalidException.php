<?php
/**
 * FileInvalidException.php
 *
 * @copyright (c) 2013, Sierra Bravo Corp., dba The Nerdery, All rights reserved
 * @license BSD-2-Clause
 */
namespace Nerdery\CsvBundle\Exception;

use \Exception;

/**
 * FileInvalidException
 *
 * @author Thomas Houfek <thomas.houfek@nerdery.com>
 */
class FileInvalidException extends Exception
{
    const DEFAULT_INVALID_MESSAGE = 'The file appears to be in an invalid format.';

    /**
     * Constructor.
     */
    public function __construct($message = null)
    {
        $message = $message
            ? $message
            : self::DEFAULT_INVALID_MESSAGE;

        parent::__construct($message);
    }
}
