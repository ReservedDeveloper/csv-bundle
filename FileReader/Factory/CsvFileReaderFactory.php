<?php
/**
 * CsvFileReaderFactory.php
 *
 * @copyright (c) 2013, Sierra Bravo Corp., dba The Nerdery, All rights reserved
 * @license BSD-2-Clause
 */
namespace Nerdery\CsvBundle\FileReader\Factory;

use Nerdery\CsvBundle\FileReader\CsvFileReader;
use Nerdery\CsvBundle\FileReader\CsvFileReaderInterface;
use Nerdery\CsvBundle\FileReader\Factory\CsvFileReaderFactoryInterface;
use Nerdery\CsvBundle\FileReader\Options\CsvFileReaderOptions;

/**
 * FileReaderFactory
 *
 * Factory that makes objects implementing the CsvFileReaderInterface.
 *
 * @author Thomas Houfek <thomas.houfek@nerdery.com>
 */
class CsvFileReaderFactory implements CsvFileReaderFactoryInterface
{

    /**
     * Create
     *
     * @param array $options
     * @return CsvFileReaderInterface
     */
    public function create(array $options = [])
    {
        $csvFileReaderOptions = new CsvFileReaderOptions($options);

        $reader =  new CsvFileReader($csvFileReaderOptions);
        return $reader;
    }
}
