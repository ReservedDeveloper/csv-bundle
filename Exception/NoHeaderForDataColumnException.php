<?php
/**
 * NoHeaderForDataColumnException.php
 *
 * @copyright (c) 2013, Sierra Bravo Corp., dba The Nerdery, All rights reserved
 * @license BSD-2-Clause
 */
namespace Nerdery\CsvBundle\Exception;

use \Exception;

/**
 * NoHeaderForDataColumnException
 *
 * @author Thomas Houfek <thomas.houfek@nerdery.com>
 */
class NoHeaderForDataColumnException extends Exception
{
    /**
     * Constructor.
     *
     * @param int $fileLineNumber The line number of the CSV file.
     * @param int $numLabels The number of columns in the header/label row.
     * @param int $numValues The number of values in the row being parsed.
     */
    public function __construct($fileLineNumber, $numLabels, $numValues)
    {
        $message =
        'Parse error in file, line ' . $fileLineNumber . ': ' .
        'The line contains ' . $numValues . ' and the header row only ' .
        'contains ' . $numLabels . ' labels.  The number of values must ' .
        'be <= the number of labels.';

        parent::__construct($message);
    }
}
