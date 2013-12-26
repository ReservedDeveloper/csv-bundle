<?php
/**
 * Created by JetBrains PhpStorm.
 * User: thoufek
 * Date: 12/26/13
 * Time: 2:11 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Nerdery\CsvBundle\FileReader\Options;


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
}