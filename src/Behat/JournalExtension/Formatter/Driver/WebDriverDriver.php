<?php

namespace Behat\JournalExtension\Formatter\Driver;

use WebDriver\Behat\WebDriverExtension\ContextInitializer;

class WebDriverDriver implements DriverInterface
{
    protected $context;

    public function __construct(ContextInitializer $context)
    {
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function getScreenshot()
    {
        return $this->context->getBrowser()->screenshot();
    }
}
