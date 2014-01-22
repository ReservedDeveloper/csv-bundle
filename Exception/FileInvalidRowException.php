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
    /**
     * Constructor.
     */
    public function __construct($message = null)
    {
        $message = $message ?
            $message :
            'The file appears to be in an invalid format.';

        parent::__construct($message);
    }
}
