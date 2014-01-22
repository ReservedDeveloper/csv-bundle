<?php
/**
 *
 * @package Nerdery\CsvBundle\FileReader\Response
 * @subpackage
 */

namespace Nerdery\CsvBundle\FileReader\Response;
use \Exception;

/**
 * Defines default behavior for responses from the file reader.
 * Used by ValidatorResponse and ParserResponse
 *
 * @package Nerdery\CsvBundle\FileReader\Response
 * @author  Daniel Lakes <dlakes@nerdery.com>
 * @version $Id$
 */
abstract class AbstractReaderResponse
{
    /**
     * @var bool
     */
    protected $success;

    /**
     * @var Exception[]|array
     */
    protected $errors;

    /**
     * construct
     *
     * @return AbstractReaderResponse $this
     */
    public function __construct()
    {
        $this->errors  = [];
        $this->success = true;
    }

    /**
     * add an error for the specified field
     *
     * @param string    $fieldName         - the name of the field which had the error.
     *                                     In the case of conflicts
     *                            (i.e. multiple calls for same fieldName)
     *                                     will append to message
     * @param Exception $error             - The error for the field
     */
    public function addErrorForField($fieldName, \Exception $error)
    {
        $this->errors[$fieldName] = $error;
        $this->success            = false;
    }

    /**
     * clear error messages and reset success
     */
    public function clearErrors()
    {
        $this->errors  = [];
        $this->success = true;
    }

    /**
     * getter for errors
     *
     * @return Exception[]|array $errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * valid message
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->success;
    }
}
