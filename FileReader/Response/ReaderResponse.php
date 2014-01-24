<?php
/**
 * ReaderResponse.php
 * @copyright (c) 2013, Sierra Bravo Corp., dba The Nerdery, All rights reserved
 * @license   BSD-2-Clause
 * @package   Nerdery\CsvBundle\FileReader\Response
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
class ReaderResponse
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
     * the array of data to process
     * for this response
     *
     * @var array
     */
    protected $rowData;

    /**
     * construct
     *
     * @return ReaderResponse $this
     */
    public function __construct()
    {
        $this->errors  = [];
        $this->success = true;
    }

    /**
     * add an error for the specified field
     *
     * @param string    $fieldName - the name of the field which had the error. In the case of conflicts (i.e. multiple calls for same fieldName) will append to message
     * @param Exception $error     - The error for the field
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

    /**
     * getter for rowData
     *
     * @return array $rowData
     * @throws Exception
     */
    public function getRowData()
    {
        if(!isset($this->rowData)){
            throw new Exception('No data has been set for this row');
        }

        return $this->rowData;
    }

    /**
     * setter for rowData
     *
     * @param array $rowData
     *
     * @return  $this
     */
    public function setRowData(array $rowData)
    {
        $this->rowData = $rowData;

        return $this;
    }

}
