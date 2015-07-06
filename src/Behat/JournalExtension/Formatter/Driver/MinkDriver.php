<?php

namespace Behat\JournalExtension\Formatter\Driver;

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Driver\SeleniumDriver;
use Behat\Mink\Mink;
use EssentialDots\Mink\Driver\WebkitDriver;

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

            return base64_decode($out);
        } elseif ($driver instanceof Selenium2Driver) {
            return base64_decode($driver->getWebDriverSession()->screenshot());
        } elseif($driver instanceof WebkitDriver) {
            return $driver->getScreenshot();
        }

        return;
    }
}
