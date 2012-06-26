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
            $this->writeln('<div class="screenshot">');
            $this->writeln(sprintf('<a href="#" class="screenshot-toggler">Toggle screenshot</a>'));
            $this->writeln(sprintf('<img style="display: none;" src="data:image/png;base64,%s" />', $screenshot));
            $this->writeln('</div>');
        }
    }

    protected function getHtmlTemplateScript()
    {
        $result = parent::getHtmlTemplateScript();

        $result .= <<<JS
        $(document).ready(function(){
            $('#behat .screenshot a').click(function(){
                $(this).parent().toggleClass('jq-toggle-opened');
            }).parent().addClass('jq-toggle');
        });
JS;

        return $result;
    }

    protected function getScreenshot(DriverInterface $driver)
    {
        if ($driver instanceof SeleniumDriver) {
            $driver->getBrowser()->captureEntirePageScreenshot("/tmp/toto", '');

            return base64_encode(file_get_contents('/tmp/toto'));
        }
    }
}
