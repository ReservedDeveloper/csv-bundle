<?php
/**
 * CsvFileReaderOptions.php
 *
 * @copyright (c) 2013, Sierra Bravo Corp., dba The Nerdery, All rights reserved
 * @license   BSD-2-Clause
 */

namespace Nerdery\CsvBundle\FileReader\Options;

use \InvalidArgumentException;
use Nerdery\CsvBundle\FileReader\Options\CsvFileReaderOptionsInterface;
use Nerdery\CsvBundle\FileReader\Parser\AbstractCsvFileRowParser;
use Nerdery\CsvBundle\FileReader\Validator\AbstractCsvFileRowValidator;

/**
 * CsvFileReaderOptions
 *
 *
 * @author Thomas Houfek <thomas.houfek@nerdery.com>
 * @author Daniel Lakes <dlakes@nerdery.com>
 */
class CsvFileReaderOptions implements CsvFileReaderOptionsInterface
{
    const OPTION_LENGTH             = 'length';
    const OPTION_DELIMITER          = 'delimiter';
    const OPTION_ENCLOSURE          = 'enclosure';
    const OPTION_ESCAPE             = 'escape';
    const OPTION_HEADER_POLICY      = 'headerPolicy';
    const OPTION_USE_LABELS_AS_KEYS = 'useLabelsAsKeys';
    const OPTION_VALIDATION         = 'validation';
    const OPTION_PARSER             = 'parser';

    const DEFAULT_LENGTH    = 0;
    const DEFAULT_DELIMITER = "\t";
    const DEFAULT_ENCLOSURE = '"';
    const DEFAULT_ESCAPE    = '\\';

    const HEADER_POLICY_NO_HEADER         = 'noHeader';
    const HEADER_POLICY_DISREGARD         = 'disregard';
    const HEADER_POLICY_SUB_DATA_OPTIONAL = "subDataOptional";
    const HEADER_POLICY_SUB_DATA_REQUIRED = "subDataRequired";
    const DEFAULT_HEADER_POLICY           = 'subDataOptional';

    const DEFAULT_VALIDATION = null;

    const DEFAULT_PARSER = null;

    const DEFAULT_USE_LABELS_AS_KEYS = true;

    /**
     * Supported options.
     *
     * @var array
     */
    private $supportedOptions = array(
        self::OPTION_LENGTH,
        self::OPTION_DELIMITER,
        self::OPTION_ENCLOSURE,
        self::OPTION_ESCAPE,
        self::OPTION_HEADER_POLICY,
        self::OPTION_USE_LABELS_AS_KEYS,
        self::OPTION_VALIDATION,
        self::OPTION_PARSER,
    );

    /**
     * Supported header policies.
     *
     * @var array
     */
    private $supportedHeaderPolicies = array(
        self::HEADER_POLICY_NO_HEADER,
        self::HEADER_POLICY_DISREGARD,
        self::HEADER_POLICY_SUB_DATA_OPTIONAL,
        self::HEADER_POLICY_SUB_DATA_REQUIRED,
    );

    /**
     * Length Option.
     *
     * @var int
     */
    private $lengthOption;

    /**
     * Delimiter Option.
     *
     * @var string
     */
    private $delimiterOption;

    /**
     * Enclosure Option.
     *
     * @var string
     */
    private $enclosureOption;

    /**
     * Escape Option.
     *
     * @var string
     */
    private $escapeOption;

    /**
     * Header Policy Option.
     *
     * @var string
     */
    private $headerPolicyOption;

    /**
     * Validation Option
     *
     * @var AbstractCsvFileRowValidator
     */
    private $validationOption;

    /**
     * Parser Option
     *
     * @var AbstractCsvFileRowParser
     */
    private $parserOption;

    /**
     * Use Labels as Keys Option.
     *
     * @var bool
     */
    private $useLabelsAsKeysOption;

    /**
     * Constructor.
     *
     * @param array $options
     *
     * @throws InvalidArgumentException If given an unsupported option.
     */
    public function __construct($options = array())
    {
        foreach ($options as $optionName => $option) {
            if (false === in_array($optionName, $this->supportedOptions)) {
                throw new InvalidArgumentException(
                    '"' . $optionName . '" is not a supported option.'
                );
            }
        }

        $this->initStandardCsvOptions($options);
        $this->initHeaderPolicyOption($options);
        $this->initValidationOption($options);
        $this->initParserOption($options);
        $this->initDataHandlingOptions($options);
    }

    /**
     * Initialize the standard CSV options.
     *
     * @param array $options
     */
    private function initStandardCsvOptions(array $options)
    {
        $this->lengthOption = isset($options[self::OPTION_LENGTH])
            ? $options['length']
            : self::DEFAULT_LENGTH;

        $this->delimiterOption = isset($options[self::OPTION_DELIMITER])
            ? $options['delimiter']
            : self::DEFAULT_DELIMITER;

        $this->enclosureOption = isset($options[self::OPTION_ENCLOSURE])
            ? $options['enclosure']
            : self::DEFAULT_ENCLOSURE;

        $this->escapeOption = isset($options[self::OPTION_ESCAPE])
            ? $options['escape']
            : self::DEFAULT_ESCAPE;
    }

    /**
     * Initialize the data handling options.
     *
     * @param array $options
     */
    private function initDataHandlingOptions(array $options)
    {
        $this->useLabelsAsKeysOption = isset($options[self::OPTION_USE_LABELS_AS_KEYS])
            ? $options[self::OPTION_USE_LABELS_AS_KEYS]
            : self::DEFAULT_USE_LABELS_AS_KEYS;
    }

    /**
     * Initialize the header policy options.
     *
     * @param $options
     *
     * @throws \InvalidArgumentException
     */
    private function initHeaderPolicyOption(array $options)
    {
        if (isset($options[self::OPTION_HEADER_POLICY])) {
            $headerPolicyOption = $options[self::OPTION_HEADER_POLICY];
            if (false === in_array(
                    $headerPolicyOption,
                    $this->supportedHeaderPolicies
                )
            ) {
                throw new InvalidArgumentException(
                    '"' . $headerPolicyOption . '" is not a supported header ' .
                    'policy option.'
                );
            }
        }

        $this->headerPolicyOption = isset($options[self::OPTION_HEADER_POLICY])
            ? $options[self::OPTION_HEADER_POLICY]
            : self::DEFAULT_HEADER_POLICY;
    }

    /**
     * Initialize the validation option.
     *
     * @param $options
     *
     * @throws \InvalidArgumentException
     */
    private function initValidationOption(array $options)
    {
        if (isset($options[self::OPTION_VALIDATION])) {
            $validationOption = $options[self::OPTION_VALIDATION];
            if (!($validationOption instanceof AbstractCsvFileRowValidator)) {
                throw new InvalidArgumentException(
                    'Provided validator must implement ' .
                    'Nerdery\CsvBundle\FileReader\Validator\CsvFileValidatorInterface.'
                );
            }
        }

        $this->validationOption = isset($options[self::OPTION_VALIDATION])
            ? $options[self::OPTION_VALIDATION]
            : self::DEFAULT_VALIDATION;
    }

    /**
     * Initialize the parser option.
     *
     * @param $options
     *
     * @throws \InvalidArgumentException
     */
    private function initParserOption(array $options)
    {
        if (isset($options[self::OPTION_PARSER])) {
            $parserOption = $options[self::OPTION_PARSER];
            if (!($parserOption instanceof AbstractCsvFileRowParser)) {
                throw new InvalidArgumentException(
                    'Provided parser must implement ' .
                    'Nerdery\CsvBundle\FileReader\Parser\CsvFileParserInterface.'
                );
            }
        }

        $this->parserOption = isset($options[self::OPTION_PARSER])
            ? $options[self::OPTION_PARSER]
            : self::DEFAULT_PARSER;
    }

    /**
     * Is header expected?
     *
     * @return bool
     */
    public function isHeaderExpected()
    {
        if (self::HEADER_POLICY_NO_HEADER === $this->headerPolicyOption) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * In the array of generated data, should we use labels as keys?
     *
     * @return bool
     */
    public function useLabelsAsKeys()
    {
        return $this->useLabelsAsKeysOption;
    }

    /**
     * Get the delimiter to use.
     *
     * @return string
     */
    public function getDelimiterOption()
    {
        return $this->delimiterOption;
    }

    /**
     * Get the enclosure to use.
     *
     * @return string
     */
    public function getEnclosureOption()
    {
        return $this->enclosureOption;
    }

    /**
     * Get the escape string to use.
     *
     * @return string
     */
    public function getEscapeOption()
    {
        return $this->escapeOption;
    }

    /**
     * Get the length of CSV file line to allow (0 to allow any length).
     *
     * @return int
     */
    public function getLengthOption()
    {
        return $this->lengthOption;
    }

    /**
     * Get the Header Policy option.
     *
     * @return string
     */
    public function getHeaderPolicyOption()
    {
        return $this->headerPolicyOption;
    }

    /**
     * Get the Validation option
     *
     * @return AbstractCsvFileRowValidator|null
     */
    public function getValidationOption()
    {
        return $this->validationOption;
    }

    /**
     * Get the Parser option
     *
     * @return AbstractCsvFileRowParser|null
     */
    public function getParserOption()
    {
        return $this->parserOption;
    }
}
