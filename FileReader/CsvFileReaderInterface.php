<?php
/**
 * CsvFileReaderInterface.php
 *
 * @copyright (c) 2013, Sierra Bravo Corp., dba The Nerdery, All rights reserved
 * @license BSD-2-Clause
 */
namespace Nerdery\CsvBundle\FileReader;

/**
 * Class CsvFileReaderInterface
 *
 * Interface for a class that reads CSV files into an associative array keyed
 * to the column labels.
 *
 * @author Thomas Houfek <thomas.houfek@nerdery.com>
 */
interface CsvFileReaderInterface {

    /**
     * Opens the file.
     *
     * @param string $path
     * @return mixed
     */
    public function open($path);

    /**
     * Closes the file.
     *
     * @return mixed
     */
    public function close();

    /**
     * Parse a file.
     *
     * Convenience function which calls open() and then parseNextRow()
     * repeatedly until the file contains no more rows.
     *
     * @param $path
     * @return mixed
     */
    public function parse($path);

    /**
     * parseNextRow
     *
     * Gets an associative array representing a data row in the CSV file.
     * The keys of the associative array are the labels in the CSV header row.
     * The values of the associative array are the values in the corresponding
     * columns of the data row.
     *
     * @return mixed
     */
    public function parseNextRow();


    /**
     * getCurrentLineNumber
     *
     * Get the current line number.  (Is useful for validation error reporting.)
     *
     * @return mixed
     */
    public function getCurrentLineNumber();



}
