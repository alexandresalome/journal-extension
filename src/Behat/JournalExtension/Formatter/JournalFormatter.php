<?php

namespace Behat\JournalExtension\Formatter;

use Behat\Behat\Definition\DefinitionInterface;
use Behat\Behat\Formatter\HtmlFormatter;
use Behat\Gherkin\Node\StepNode;
use Behat\Mink\Mink;
use Behat\Mink\Driver\DriverInterface;

use Behat\Mink\Driver\SeleniumDriver;

class JournalFormatter extends HtmlFormatter
{
    protected $mink;

    public function __construct(Mink $mink)
    {
        $this->mink = $mink;

        parent::__construct();
    }

    protected function printStepBlock(StepNode $step, DefinitionInterface $definition = null, $color)
    {
        parent::printStepBlock($step, $definition, $color);

        $driver = $this->mink->getSession()->getDriver();
        if ($screenshot = $this->getScreenshot($driver)) {
            $this->writeln(sprintf('<img src="data:image/png;base64,%s" />', $screenshot));
        }
    }

    protected function getScreenshot(DriverInterface $driver)
    {
        if ($driver instanceof SeleniumDriver) {
            $driver->getBrowser()->captureEntirePageScreenshot("/tmp/toto", '');

            return base64_encode(file_get_contents('/tmp/toto'));
        }
    }
}
