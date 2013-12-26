<?php
/**
 * CsvFileReaderFactory.php
 *
 * @copyright (c) 2013, Sierra Bravo Corp., dba The Nerdery, All rights reserved
 * @license BSD-2-Clause
 */
namespace Nerdery\CsvBundle\FileReader\Factory;

use Nerdery\CsvBundle\FileReader\CsvFileReader;
use Nerdery\CsvBundle\FileReader\FileReaderInterface;
use Nerdery\CsvBundle\FileReader\Factory\FileReaderFactoryInterface;

/**
 * FileReaderFactory
 *
 * Factory that makes objects implementing the CsvFileReaderInterface.
 *
 * @author Thomas Houfek <thomas.houfek@nerdery.com>
 */
class CsvFileReaderFactory implements CsvFileReaderFactoryInterface {

    /**
     * Create
     *
     * @param array $options
     * @return FileReaderInterface
     */
    public function create(array $options = [])
    {
        $supportedOptions = [
            'length',
            'delimiter',
            'enclosure',
            'escape',
            'enforce-complete-data-rows',
        ];

        foreach ($options as $option) {
            if (false === in_array($option, $supportedOptions)) {
                throw new \InvalidArgumentException(
                    '"' . $option . '" is not a supported option.'
                );
            }
        }

        $reader =  new CsvFileReader($options);
        return $reader;
    }
}
