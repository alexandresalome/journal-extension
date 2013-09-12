<?php

namespace Behat\JournalExtension\Formatter;

use Behat\Behat\Formatter\FormatterDispatcher;
use Behat\JournalExtension\Formatter\Driver\DriverInterface;

class JournalDispatcher extends FormatterDispatcher
{
    protected $driver;
    protected $captureAll;

    public function __construct(DriverInterface $driver, $captureAll = false)
    {
        $this->driver = $driver;
        $this->captureAll = $captureAll;

        parent::__construct(
            'Behat\JournalExtension\Formatter\JournalFormatter',
            'journal',
            'HTML with screenshots'
        );
    }

    public function createFormatter()
    {
        return new JournalFormatter($this->driver, $this->captureAll);
    }
}
