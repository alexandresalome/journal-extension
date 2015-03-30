Behat - Journal Extension
=========================

This extension provides a HTML format for Behat with screenshots.

Installation
------------

Add it to your ``composer.json``:

.. code-block:: json

    {
        "require": {
            "alom/journal-extension": "dev-master"
        }
    }

Configure ``behat.yml``:

.. code-block:: yaml

    default:
        extensions:
            Behat\JournalExtension\Extension:
                driver: mink # available: mink, webdriver
                capture_all: true # defaults to false to only capture on failure

Launch your test suite with format **journal**:

.. code-block:: bash

    $ bin/behat -f journal --out journal.html [feature, ...]

This command will produce a file ``journal.html`` containing the HTML standard
output with additional screenshots.

Screenshot files will be placed in the same folder as the main output file.
Any old screenshots are removed from the output folder first.

To get another progress on screen while journal report is being generated, use behat.yml:

.. code-block:: yaml

    formatter:
            name: journal,pretty
            parameters:
                output_path: wwwdocs/features/index.html,null

To work, you have to use proper extension for it. Supported are:

* `PHP WebDriver <https://github.com/alexandresalome/php-webdriver>`_
* Mink (with SeleniumDriver or Selenium2Driver)
