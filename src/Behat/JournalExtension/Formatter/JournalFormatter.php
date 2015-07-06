<?php

namespace Behat\JournalExtension\Formatter;

use Behat\Behat\DataCollector\LoggerDataCollector;
use Behat\Behat\Definition\DefinitionInterface;
use Behat\Behat\Formatter\HtmlFormatter;
use Behat\Gherkin\Node\StepNode;
use Behat\JournalExtension\Formatter\Driver\DriverInterface;
use Behat\Mink\Mink;
use Behat\Behat\Event\StepEvent;
use Behat\Gherkin\Node\TableNode;


class JournalFormatter extends HtmlFormatter {
    protected $driver;
    protected $captureAll;
    protected $screenShotMarkup;
    protected $screenShotDirectory;

    public function __construct(DriverInterface $driver, $captureAll) {
        $this->driver = $driver;
        $this->captureAll = $captureAll;
        $this->screenShotMarkup = '';
        $this->screenShotPrefix = 'screenshot_';
        parent::__construct();
    }

    protected function createOutputConsole() {
        $output_path = $this->parameters->get('output_path');
        if (!$output_path) {
            $output_path = '.';
        }
        $this->screenShotDirectory = dirname($output_path);
        array_map('unlink', glob($this->screenShotDirectory . DIRECTORY_SEPARATOR . $this->screenShotPrefix . "*.png"));
        return parent::createOutputConsole();
    }

    /**
     * {@inheritdoc}
     */
    protected function printSummary(LoggerDataCollector $logger) {
        $results = $logger->getScenariosStatuses();
        $result = $results['failed'] > 0 ? 'failed' : 'passed';

        parent::printSummary($logger);

        $this->writeln('<div class="summary ' . $result . '">');
        $this->writeln(<<<'HTML'
<div class="switchers screenshot-switchers">
    <a href="#" onclick="$('.screenshot,.outline-example-result-screenshots-holder').addClass('jq-toggle-opened'); $('#behat_show_all').click(); return false;" id="behat_show_screenshots">[+] screenshots</a>
    <a href="#" onclick="$('.screenshot,.outline-example-result-screenshots-holder').removeClass('jq-toggle-opened'); $('#behat_hide_all').click(); return false;" id="behat_hide_screenshots">[-] screenshots</a>
</div>
HTML
        );
        $this->writeln('</div>');


    }

    /**
     * Listens to "step.after" event.
     *
     * @param StepEvent $event
     *
     * @uses printStep()
     */
    public function afterStep(StepEvent $event) {
        $color = $this->getResultColorCode($event->getResult());
        $capture = $this->captureAll || $color == 'failed';
        if ($capture) {
            try {
                $screenshot = $this->driver->getScreenshot();
                if ($screenshot) {
                    $date = new \DateTime('now');
                    $fileName = $this->screenShotPrefix . $date->format('Y-m-d H.i.s') . '.png';
                    $file = $this->screenShotDirectory . DIRECTORY_SEPARATOR . $fileName;
                    file_put_contents($file, $screenshot);
                    $this->screenShotMarkup .= '<div class="screenshot">';
                    $this->screenShotMarkup .= sprintf('<a href="#" class="screenshot-toggler">Toggle screenshot for ' . $event->getStep()->getText() . '</a>');
                    $this->screenShotMarkup .= sprintf('<img src="%s" />', $fileName);
                    $this->screenShotMarkup .= '</div>';
                }
            } catch (\Exception $e) {
                $this->screenShotMarkup .= '<div class="screenshot">';
                $this->screenShotMarkup .= sprintf('<em>Error while taking screenshot for ' . $event->getStep()->getText() . ' : %s</em>', htmlspecialchars($e->getMessage()));
                $this->screenShotMarkup .= '</div>';
            }
        }
        if ($this->inBackground && $this->isBackgroundPrinted) {
            return;
        }

        if (!$this->inBackground && $this->inOutlineExample) {
            $this->delayedStepEvents[] = $event;

            return;
        }

        $this->printStep(
            $event->getStep(),
            $event->getResult(),
            $event->getDefinition(),
            $event->getSnippet(),
            $event->getException()
        );
        $this->writeln($this->screenShotMarkup);
        $this->screenShotMarkup = '';

    }


    /**
     * {@inheritdoc}
     * @param TableNode $examples
     * @param int $iteration
     * @param int $result
     * @param bool $isSkipped
     */
    protected function printOutlineExampleResult(TableNode $examples, $iteration, $result, $isSkipped) {
        if (!$this->getParameter('expand')) {
            $color = $this->getResultColorCode($result);
            $this->printColorizedTableRow($examples->getRow($iteration + 1), $color);

            $this->printOutlineExampleResultExceptions($examples, $this->delayedStepEvents);
            $this->writeln('<tr class="' . $color . '">');
            $this->writeln('<td>' . ($iteration + 1) . '</td>');
            $this->writeln('<td colspan="' . count($examples->getRow($iteration)) . '">');
            $this->writeln('<div><a href="#" class="open-screenshots"> [+] Screenshot links </a>&nbsp;<a href="#" class="close-screenshots"> [-] Screenshot links </a></div>');
            $this->writeln('<div class="outline-example-result-screenshots-holder jq-toggle">');
            $this->writeln($this->screenShotMarkup);
            $this->writeln('</div>');
            $this->writeln('</td>');
            $this->writeln('</tr>');
            $this->screenShotMarkup = '';
        } else {
            $this->write('<h4>' . $examples->getKeyword() . ': ');
            foreach ($examples->getRow($iteration + 1) as $value) {
                $this->write('<span>' . $value . '</span>');
            }
            $this->writeln('</h4>');

            foreach ($this->delayedStepEvents as $event) {
                $this->writeln('<ol>');
                $this->printStep(
                    $event->getStep(),
                    $event->getResult(),
                    $event->getDefinition(),
                    $event->getSnippet(),
                    $event->getException()
                );
                $this->writeln('</ol>');
            }
        }
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    protected function getHtmlTemplateScript() {
        $result = parent::getHtmlTemplateScript();

        $result .= <<<JS
        $(document).ready(function(){
            $('#behat .screenshot a').click(function(){
                $(this).parent().toggleClass('jq-toggle-opened');

                return false;
            }).parent().addClass('jq-toggle');
            $('a.open-screenshots').click(function(){

                $(this).closest('tr').find('.outline-example-result-screenshots-holder.jq-toggle').addClass('jq-toggle-opened');

                return false;
            });
            $('a.close-screenshots').click(function(){

                $(this).closest('tr').find('.outline-example-result-screenshots-holder.jq-toggle').removeClass('jq-toggle-opened');

                return false;
            });
        });
JS;

        return $result;
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    protected function getHtmlTemplateStyle() {
        $result = parent::getHtmlTemplateStyle();

        $result .= <<<CSS

        #behat .screenshot img {
            display: none;
            max-width: 100%;
        }

        #behat .screenshot {
            clear: both;
        }

        #behat .screenshot.jq-toggle-opened img {
            display: block;
        }

        #behat .outline-example-result-screenshots-holder.jq-toggle {
            display: none;
        }
        #behat .outline-example-result-screenshots-holder.jq-toggle.jq-toggle-opened {
            display: block;
        }
        #behat .summary .screenshot-switchers {
            right: 114px;
        }

CSS;

        return $result;
    }

}
