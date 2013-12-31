<?php
/**
 * Reporter.php
 *
 * @copyright (c) 2013, Sierra Bravo Corp., dba The Nerdery, All rights reserved
 * @license BSD-2-Clause
 */
namespace Nerdery\CsvBundle\Error;

/**
 * Reporter
 *
 * @author Thomas Houfek <thomas.houfek@nerdery.com>
 */
class Reporter
{

    /**
     * General errors.
     *
     * @var array
     */
    private $generalErrors;

    /**
     * Error Messages by Line
     *
     * @var array
     */
    private $lineErrors;

    /**
     * Has error
     *
     * @var bool
     */
    private $hasError;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->lineErrors    = [];
        $this->generalErrors = [];
        $this->hasError      = false;
    }

    /**
     * addError
     *
     * Add an error.  If a line number is supplied, the error will be stored
     * in a sub-array of $lineErrors, keyed to the line number.  If no line
     * number is supplied, the error will be stored in $generalErrors.
     *
     * @param string $errorMsg
     * @param int $lineNumber Line number (optional).
     */
    public function addError($errorMsg, $lineNumber = null)
    {
        $this->hasError = true;
        if (null === $lineNumber) {
            $this->addGeneralError($errorMsg);
        } else {
            $this->addLineError($errorMsg, $lineNumber);
        }
    }

    /**
     * addGeneralError
     *
     * Add an error message to the General Errors array.
     *
     * @param string $errorMsg
     */
    private function addGeneralError($errorMsg)
    {
        $this->generalErrors[] = $errorMsg;
    }

    /**
     * addLineError
     *
     * Add an error message to the Line Errors array.
     *
     * @param string $errorMsg
     * @param int $lineNumber
     */
    public function addLineError($errorMsg, $lineNumber)
    {

        if (false === isset($this->lineErrors["$lineNumber"])) {
            $this->lineErrors["$lineNumber"] = [];
        }
        $this->lineErrors["$lineNumber"][] = $errorMsg;
    }

    /**
     * getGeneralErrors
     *
     * Get the General Errors array.
     *
     * @return array
     */
    public function getGeneralErrors()
    {
        return $this->lineErrors;
    }

    /**
     * getLineErrors
     *
     * Get the Line Errors array.
     *
     * @return array
     */
    public function getLineErrors()
    {
        return $this->lineErrors;
    }

    /**
     * Has error?
     *
     * @return boolean
     */
    public function hasError()
    {
        return $this->hasError;
    }
}
