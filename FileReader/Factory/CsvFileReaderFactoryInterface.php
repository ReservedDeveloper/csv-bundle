<?php
/**
 * CsvFileReaderFactoryInterface.php
 *
 * @copyright (c) 2013, Sierra Bravo Corp., dba The Nerdery, All rights reserved
 * @license BSD-2-Clause
 */
namespace Nerdery\CsvBundle\FileReader\Factory;
use Nerdery\CsvBundle\FileReader\CsvFileReaderInterface;

/**
 * FileReaderFactoryInterface
 *
 * The interface for a factory that makes objects implementing the
 * FileReaderInterface.
 *
 * @author Thomas Houfek <thomas.houfek@nerdery.com>
 */
interface CsvFileReaderFactoryInterface {

    /**
     * Create
     *
     * @param array $options
     * @return CsvFileReaderInterface
     */
    public function create(array $options = array());

}
