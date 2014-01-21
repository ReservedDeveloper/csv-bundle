<?php
/**
 * InvalidFileHeaderException.php
 *
 * @copyright (c) 2013, Sierra Bravo Corp., dba The Nerdery, All rights reserved
 * @license BSD-2-Clause
 */
namespace Nerdery\CsvBundle\Exception;

use \Exception;

/**
 * InvalidFileHeaderException
 *
 * @author Daniel Lakes <dlakes@nerdery.com>
 */
class InvalidFileHeaderException extends Exception
{
    /**
     * constructor
     * @param string $message - message to display
     */
    public function __construct($message = null)
    {
        $message = $message ?
            $message :
            'The specified file headers do not match those dictated by the options.';

        parent::__construct($message);
    }
}
