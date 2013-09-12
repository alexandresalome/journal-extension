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

Launch your test suite with format **journal**:

.. code-block:: bash

    $ bin/behat -f journal --out journal.html [feature, ...]

This command will produce a file ``journal.html`` containing the HTML standard
output with additional screenshots.

To work, you have to use proper extension for it. Supported are:

* `PHP WebDriver <https://github.com/alexandresalome/php-webdriver>`_
* Mink (with SeleniumDriver or Selenium2Driver)
