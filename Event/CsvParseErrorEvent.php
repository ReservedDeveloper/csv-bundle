<?php
/**
 * CsvParseErrorEvent.php
 *
 * @copyright (c) 2013, Sierra Bravo Corp., dba The Nerdery, All rights reserved
 * @license BSD-2-Clause
 */
namespace Nerdery\CsvBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use \Exception;

/**
 * CsvParseErrorEvent
 *
 * @author Thomas Houfek <thomas.houfek@nerdery.com>
 */
class CsvParseErrorEvent extends Event {

    const EVENT_KEY = 'nerdery.csvbundle.parse_error';

    /**
     * Exception
     *
     * @var Exception
     */
    private $exception;

    /**
     * Line number
     *
     * @var int
     */
    private $lineNumber;

    /**
     * Constructor.
     *
     * @param Exception $e
     * @param int $lineNumber
     */
    public function __construct(Exception $e, $lineNumber = null)
    {
        $this->exception  = $e;
        $this->lineNumber = $lineNumber;
    }

    /**
     * Get Exception.
     *
     * @return Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Get Line Number.
     *
     * @return int|null
     */
    public function getLineNumber()
    {
        return $this->lineNumber;
    }
}