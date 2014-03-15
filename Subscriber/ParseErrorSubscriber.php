<?php
/**
 * ParseErrorSubscriber.php
 *
 * @copyright (c) 2013, Sierra Bravo Corp., dba The Nerdery, All rights reserved
 * @license BSD-2-Clause
 */

namespace Nerdery\CsvBundle\Subscriber;


use Nerdery\CsvBundle\Event\CsvParseErrorEvent;
use Nerdery\CsvBundle\Error\Reporter as ErrorReporter;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * ParseErrorSubscriber
 *
 * @author Thomas Houfek <thomas.houfek@nerdery.com>
 */
class ParseErrorSubscriber implements EventSubscriberInterface
{
    const UPDATE_REPORTER_PRIORITY = 100;

    /**
     * @param ErrorReporter $errorReporter
     */
    public function __construct(
        ErrorReporter $errorReporter
    ) {
        $this->errorReporter = $errorReporter;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            CsvParseErrorEvent::EVENT_KEY => array(
                array(
                    'updateReporter', static::UPDATE_REPORTER_PRIORITY,
                ),
            ),
        );
    }


    /**
     * Update the Error Reporter object.
     *
     * Gets the exception message and the line number from the event and adds
     * them to the Reporter object.
     *
     * @param CsvParseErrorEvent $event
     */
    public function updateReporter(CsvParseErrorEvent $event)
    {
        $exception  = $event->getException();
        $lineNumber = $event->getLineNumber();

        $this->errorReporter->addError(
            $exception->getMessage(),
            $lineNumber
        );
    }

}