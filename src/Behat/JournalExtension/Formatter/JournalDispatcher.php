<?php

namespace Behat\JournalExtension\Formatter;

use Behat\Behat\Formatter\FormatterDispatcher;
use Behat\Mink\Mink;

class JournalDispatcher extends FormatterDispatcher
{
    protected $mink;
    protected $captureAll;

    public function __construct(Mink $mink, $captureAll)
    {
        $this->mink = $mink;
        $this->captureAll = $captureAll;

        parent::__construct(
            'Behat\JournalExtension\Formatter\JournalFormatter',
            'journal',
            'HTML with screenshots'
        );
    }

    public function createFormatter()
    {
        return new JournalFormatter($this->mink, $this->captureAll);
    }
}
