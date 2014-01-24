<?php
/**
 * CsvFileReaderOptionsInterface.php
 *
 * @copyright (c) 2013, Sierra Bravo Corp., dba The Nerdery, All rights reserved
 * @license BSD-2-Clause
 */

namespace Nerdery\CsvBundle\FileReader\Options;

use Nerdery\CsvBundle\FileReader\Parser\AbstractCsvFileRowParser;
use Nerdery\CsvBundle\FileReader\Validator\AbstractCsvFileRowValidator;

interface CsvFileReaderOptionsInterface
{
    /**
     * Is header expected?
     *
     * Returns true if we expect to find a header row in the CSV file.
     *
     * @return bool
     */
    public function isHeaderExpected();

    /**
     * In the array of generated data, should we use labels as keys?
     *
     * @return bool
     */
    public function useLabelsAsKeys();

    /**
     * Get the delimiter to use.
     *
     * @return string
     */
    public function getDelimiterOption();

    /**
     * Get the enclosure to use.
     *
     * @return string
     */
    public function getEnclosureOption();

    /**
     * Get the escape string to use.
     *
     * @return string
     */
    public function getEscapeOption();

    /**
     * Get the length of CSV file line to allow (0 to allow any length).
     *
     * @return int
     */
    public function getLengthOption();

    /**
     * Get the Header Policy option.
     *
     * @return string
     */
    public function getHeaderPolicyOption();

    /**
     * Get the Validation option
     *
     * @return AbstractCsvFileRowValidator|null
     */
    public function getValidationOption();

    /**
     * Get the Parser option
     *
     * @return AbstractCsvFileRowParser|null
     */
    public function getParserOption();
}