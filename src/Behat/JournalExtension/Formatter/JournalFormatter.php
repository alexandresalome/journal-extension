<?php

namespace Behat\JournalExtension\Formatter;

use Behat\Behat\DataCollector\LoggerDataCollector;
use Behat\Behat\Definition\DefinitionInterface;
use Behat\Behat\Formatter\HtmlFormatter;
use Behat\Gherkin\Node\StepNode;
use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Driver\SeleniumDriver;
use Behat\Mink\Mink;

class JournalFormatter extends HtmlFormatter
{
    protected $mink;
    protected $captureAll;

    public function __construct(Mink $mink, $captureAll)
    {
        $this->mink = $mink;
        $this->captureAll = $captureAll;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function printSummary(LoggerDataCollector $logger)
    {
        $this->writeln(<<<'HTML'
<div class="switchers">
    <a href="#" onclick="$('.screenshot').addClass('jq-toggle-opened'); $('#behat_show_all').click(); return false;" id="behat_show_screenshots">[+] screenshots</a>
    <a href="#" onclick="$('.screenshot').removeClass('jq-toggle-opened'); $('#behat_hide_all').click(); return false;" id="behat_hide_screenshots">[-] screenshots</a>
</div>
HTML
);
        parent::printSummary($logger);
    }

    protected function printStepBlock(StepNode $step, DefinitionInterface $definition = null, $color)
    {
        parent::printStepBlock($step, $definition, $color);

        $driver = $this->mink->getSession()->getDriver();

        $capture = true || $this->captureAll || $color == 'failed';

        if ($capture) {
            try {
                $screenshot = $this->getScreenshot($driver);
                if ($screenshot) {
                    $this->writeln('<div class="screenshot">');
                    $this->writeln(sprintf('<a href="#" class="screenshot-toggler">Toggle screenshot</a>'));
                    $this->writeln(sprintf('<img src="data:image/png;base64,%s" />', $screenshot));
                    $this->writeln('</div>');
                }
            } catch (\Exception $e) {
                $this->writeln('<div class="screenshot">');
                $this->writeln(sprintf('<em>Error while taking screenshot</em>'));
                $this->writeln('</div>');
            }
        }
    }

    protected function getHtmlTemplateScript()
    {
        $result = parent::getHtmlTemplateScript();

        $result .= <<<JS
        $(document).ready(function(){
            $('#behat .screenshot a').click(function(){
                $(this).parent().toggleClass('jq-toggle-opened');

                return false;
            }).parent().addClass('jq-toggle');
        });
JS;

        return $result;
    }

    protected function getHtmlTemplateStyle()
    {
        $result = parent::getHtmlTemplateStyle();

        $result .= <<<CSS

        .screenshot img {
            display: none;
        }

        .screenshot.jq-toggle-opened img {
            display: block;
        }

CSS;

        return $result;
    }

    protected function getScreenshot(DriverInterface $driver)
    {
        if ($driver instanceof SeleniumDriver) {
            $out = $driver->getBrowser()->captureEntirePageScreenshotToString("");
            $out = str_replace("\n", "", $out);

            return $out;
        } elseif ($driver instanceof Selenium2Driver) {
            return $driver->getWebDriverSession()->screenshot();
        }
    }
}
