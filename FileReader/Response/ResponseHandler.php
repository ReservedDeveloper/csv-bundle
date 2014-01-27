<?php
/**
 * ResponseHandler.php
 * @copyright (c) 2013, Sierra Bravo Corp., dba The Nerdery, All rights reserved
 * @license   BSD-2-Clause
 * @package   Nerdery\CsvBundle\FileReader\Response
 */
 
namespace Nerdery\CsvBundle\FileReader\Response;
use Nerdery\CsvBundle\Exception\FileInvalidRowException;
use Nerdery\CsvBundle\Exception\InvalidDataFieldException;

/**
 * Specifies base structure for use by objects utilizing a ReaderResponse
 *
 * @package Nerdery\CsvBundle\FileReader\Validator
 * @author  Daniel Lakes <dlakes@nerdery.com>
 * @version $Id$
 */
class ResponseHandler {

    /**
     * @var ReaderResponse
     */
    private $readerResponse;

    public function __construct()
    {
        $this->setResponse(new ReaderResponse());
    }

    /**
     * setter. extending methods must call parent to set var
     *
     * @param ReaderResponse $readerResponse
     */
    public function setResponse(ReaderResponse $readerResponse){
        $this->readerResponse = $readerResponse;
    }

    /**
     * response getter. Should likely not be
     * accessed internally by the class
     * without $cloneAndReset == true to avoid accidental
     * reset of values
     *
     * Convenience methods ({@link getResponseData()} and {@link isResponseValid()})
     * have been created for the most commonly accessed components
     *
     * @param bool $cloneAndReset - whether clone the object
     * for return and reset the response.
     * Defaults to true under assumption that callers
     * are only retrieving response once
     * in a given response cycle
     *
     * @return ReaderResponse
     */
    public function getResponse($cloneAndReset = true)
    {
        if($cloneAndReset){
            $response = clone $this->readerResponse;
            $this->resetResponse();

            return $response;
        } else {
            return $this->readerResponse;
        }
    }

    /**
     * gets response data
     *
     * @return array $rowData
     */
    protected function getResponseData()
    {
       return $this->getResponse(false)->getRowData();
    }

    /**
     * updates row data w/ new values
     * used mainly by classes updating the values, but not wanting to reset the response
     */
    protected function updateData(array $updatedData)
    {
        $this->getResponse(false)->setRowData($updatedData);
    }

    /**
     * get the current state of the response object
     *
     * @return bool
     */
    protected function isResponseValid()
    {
        return $this->getResponse(false)->isValid();
    }

    /**
     * reset the response object
     *
     * @param array $rowData - the data with which to reset the response
     */
    public function resetResponse(array $rowData = null)
    {
        $response = new ReaderResponse();

        if(!empty($rowData)){
            $response->setRowData($rowData);
        }

        $this->setResponse($response);
    }

    /**
     * generate a new exception for the field
     * and add to our error queue
     *
     * @param string $fieldKey
     * @param mixed $fieldValue
     * @param string|null $message - the message to pass along. Generic one is
     *                               generated based on $fieldKey and
     *                               $fieldValue if none is provided
     */
    public function addFieldErrorForResponse($fieldKey, $fieldValue, $message = null){
        $message = !empty($message)
            ? $message
            : $this->generateGenericInvalidFieldMessage($fieldKey, $fieldValue, $message);

        $exception = new InvalidDataFieldException($message);
        $this->getResponse(false)->addErrorForField($fieldKey, $exception);
    }

    /**
     * Generate a new exception for the row and
     * add to the error queue
     * @param string|null $message
     */
    public function addRowErrorForResponse($message = null){
        $message = !empty($message)
            ? $message
            : 'There was an error detected within the row.';

        $exception = new FileInvalidRowException($message);
        $this->getResponse(false)->addErrorForRow($exception);
    }

    /**
     * @param string|int $fieldKey  - the name of the field, or key if none
     * @param mixed      $fieldValue - the value provided for the field
     *
     * @return string
     */
    private function generateGenericInvalidFieldMessage($fieldKey, $fieldValue)
    {
        $message = "The value %s for the specified field %s does not match expected format";

        return $this->generateInvalidFieldMessage($fieldKey, $fieldValue, $message);
    }

    /**
     * insert $fieldKey, $fieldValue into provided $sprintfMsg
     * @param string $fieldKey
     * @param string $fieldValue
     * @param string $sprintfMsg - a string formatted in the fashion of {@link sprintf()}.
     *                             Placeholders should be for fieldKey and fieldValue, in that order
     *
     * @return string the message with formatted vars inserted
     */
    protected function generateInvalidFieldMessage($fieldKey, $fieldValue, $sprintfMsg)
    {
        $fieldKey = is_string($fieldKey) || is_int($fieldKey)
            ? "'$fieldKey'"
            : "";

        $fieldValue = is_string($fieldValue) || is_object($fieldValue) && method_exists($fieldValue, '__toString')
            ? "'$fieldValue'"
            : "";

        $sprintfMsg = is_string($sprintfMsg)
            ? $sprintfMsg
            : "";

        return sprintf($sprintfMsg, $fieldKey, $fieldValue);
    }
}
