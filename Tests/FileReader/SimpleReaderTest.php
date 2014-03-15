<?php
/**
 * CsvFileReaderTest.php
 *
 * @copyright (c) 2013, Sierra Bravo Corp., dba The Nerdery, All rights reserved
 * @license BSD-2-Clause
 */

namespace Nerdery\CsvBundle\Tests\FileReader;

use Nerdery\CsvBundle\FileReader\CsvFileReader;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * CsvFileReaderTest
 *
 * @author Thomas Houfek <thomas.houfek@nerdery.com>
 */
class CsvFileReaderTest extends TestCase {

    /**
     * testImportsValidFileWithNoColumnVariance()
     *
     * Tests that we can import a valid file when there is no difference
     * between the number of columns in the header/label row and the number
     * of columns in any of the data rows.
     */
    public function testImportsValidFileWithNoColumnVariance()
    {
        $this->markTestIncomplete('need to update this test class to inject additional dependencies');
        $reader = new CsvFileReader(
            array()
        );

        $reader->open(__DIR__ . '/../../TestFiles/validWithNoColumnVariance.csv');

        $rowOne = $reader->getRowData();
        $this->assertEquals(
            'apple', $rowOne['fruitName']
        );
        $this->assertEquals(
            'red', $rowOne['color']
        );
        $this->assertEquals(
            'round', $rowOne['shape']
        );
        $this->assertEquals(
            'crisp', $rowOne['taste']
        );

        $rowTwo = $reader->getRowData();
        $this->assertEquals(
            'banana', $rowTwo['fruitName']
        );
        $this->assertEquals(
            'yellow', $rowTwo['color']
        );
        $this->assertEquals(
            'long', $rowTwo['shape']
        );
        $this->assertEquals(
            'creamy', $rowTwo['taste']
        );

        $rowThree = $reader->getRowData();
        $this->assertEquals(
            'pear', $rowThree['fruitName']
        );
        $this->assertEquals(
            'green', $rowThree['color']
        );
        $this->assertEquals(
            '"pear-shaped"', $rowThree['shape']
        );
        $this->assertEquals(
            'crisp', $rowThree['taste']
        );

        $rowFour = $reader->getRowData();
        $this->assertFalse($rowFour);
    }

    /**
     * testImportsValidFileWithColumnVariance()
     *
     * Tests that we can import a valid file with data rows whose number of
     * columns is variable (but does not exceed the number of columns in the
     * header).
     */
    public function testImportsValidFileWithColumnVariance()
    {
        $this->markTestIncomplete('need to update this test class to inject additional dependencies');

        $reader = new CsvFileReader(
            array()
        );

        $reader->open(__DIR__ . '/../../TestFiles/validWithNoColumnVariance.csv');

        $rowOne = $reader->getRowData();
        $this->assertEquals(
            'apple', $rowOne['fruitName']
        );
        $this->assertEquals(
            'red', $rowOne['color']
        );
        $this->assertEquals(
            'round', $rowOne['shape']
        );
        $this->assertEquals(
            'crisp', $rowOne['taste']
        );
        $this->assertEquals(
            'Red Delicious', $rowOne['variety1']
        );
        $this->assertEquals(
            'Golden Delicious', $rowOne['variety2']
        );
        $this->assertEquals(
            'Fuji', $rowOne['variety3']
        );

        $rowTwo = $reader->getRowData();
        $this->assertEquals(
            'banana', $rowTwo['fruitName']
        );
        $this->assertEquals(
            'yellow', $rowTwo['color']
        );
        $this->assertEquals(
            'long', $rowTwo['shape']
        );
        $this->assertEquals(
            'creamy', $rowTwo['taste']
        );
        $this->assertEquals(
            'Plantain', $rowTwo['variety1']
        );
        $this->assertNull($rowTwo['variety2']);
        $this->assertNull($rowTwo['variety3']);

        $rowThree = $reader->getRowData();
        $this->assertEquals(
            'pear', $rowThree['fruitName']
        );
        $this->assertEquals(
            'green', $rowThree['color']
        );
        $this->assertEquals(
            '"pear-shaped"', $rowThree['shape']
        );
        $this->assertEquals(
            'crisp', $rowThree['taste']
        );
        $this->assertEquals(
            'Green Anjou', $rowThree['variety1']
        );
        $this->assertEquals(
            'Red Anjou', $rowThree['variety2']
        );
        $this->assertNull($rowThree['variety3']);

        $rowFour = $reader->getRowData();
        $this->assertFalse($rowFour);
    }

    /**
     * testThrowsExceptionIfTooManyColumnsInDataRow()
     *
     * Tests that an exception is thrown if the number of columns in a data
     * row exceeds the number of columns in the header/label row.
     */
    public function testThrowsExceptionIfTooManyColumnsInDataRow()
    {
        $this->markTestIncomplete('need to update this test class to inject additional dependencies');

        $reader = new CsvFileReader(
            array()
        );

        $reader->open(__DIR__ . '/../../TestFiles/invalidTooManyColumnsInDataRows.csv');


        $this->setExpectedException(
            'Nerdery\CsvBundle\Exception\NoHeaderForDataColumnException',
            'Parse error in file, line 2: The line contains 8 and the ' .
            'header row only contains 7 labels.  The number of values must ' .
            'be <= the number of labels.'
        );

        $rowOne = $reader->getRowData();

    }
}
