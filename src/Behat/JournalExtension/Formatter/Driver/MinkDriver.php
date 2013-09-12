<?php

namespace Behat\JournalExtension\Formatter\Driver;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Driver\SeleniumDriver;

class MinkDriver implements DriverInterface
{
    protected $mink;

    public function __construct(Mink $mink)
    {
        $this->mink = $mink;
    }

    /**
     * {@inheritdoc}
     */
    public function getScreenshot()
    {
        $driver = $this->mink->getSession()->getDriver();

        if ($driver instanceof SeleniumDriver) {
            $out = $driver->getBrowser()->captureEntirePageScreenshotToString("");
            $out = str_replace("\n", "", $out);

            return $out;
        } elseif ($driver instanceof Selenium2Driver) {
            return $driver->getWebDriverSession()->screenshot();
        }

        return $this->mink;
    }
}
