<?php

namespace Behat\JournalExtension\Formatter\Driver;

interface DriverInterface
{
    /**
     * Returns a screenshot in PNG format.
     *
     * @return string
     */
    public function getScreenshot();
}
