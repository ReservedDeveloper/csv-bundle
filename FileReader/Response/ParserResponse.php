<?php
/**
 * ParserResponse.php
 *
 * @copyright (c) 2013, Sierra Bravo Corp., dba The Nerdery, All rights reserved
 * @license   BSD-2-Clause
 * @package   Nerdery\CsvBundle\FileReader\Parser
 */

namespace Nerdery\CsvBundle\FileReader\Response;

use \Exception;

/**
 * Specifies parser response for use by the AbstractCsvFileParser
 *
 * @package Nerdery\CsvBundle\FileReader\Parser
 * @author  Daniel Lakes <dlakes@nerdery.com>
 * @version $Id$
 */
class ParserResponse extends AbstractReaderResponse
{
    /**
     * the row of parsed data
     *
     * @var array
     */
    protected $parsedRow;

    /**
     * construct
     *
     * @return ParserResponse $this
     */
    public function __construct()
    {
        parent::__construct();
        $this->parsedRow = [];
    }

    /**
     * the data that has been successfully parsed
     *
     * @param array $parsedDataRow
     */
    public function setParsedData(array $parsedDataRow)
    {
        $this->parsedRow = $parsedDataRow;
    }

    /**
     * @return array
     */
    public function getParsedData()
    {
        return $this->parsedRow;
    }
}
