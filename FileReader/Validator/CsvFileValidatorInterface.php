<?php
/**
 * CsvFileValidatorInterface
 *
 * @copyright (c) 2013, Sierra Bravo Corp., dba The Nerdery, All rights reserved
 * @license BSD-2-Clause
 *  
 * @package Nerdery\CsvBundle\FileReader\Validator
 */
 
namespace Nerdery\CsvBundle\FileReader\Validator;

/**
 * Specifies base validator structure for use by the file reader
 *
 * @package Nerdery\CsvBundle\FileReader\Validator
 * @author Daniel Lakes <dlakes@nerdery.com>
 * @version $Id$
 */ 
interface CsvFileValidatorInterface {

    /**
     * validate the header row
     *
     * @param array $headerRow
     *
     * @return bool - true on validation success, false on failure
     */
    public function validateHeader(array $headerRow);
}
