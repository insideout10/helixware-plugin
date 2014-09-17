[![Build Status](https://secure.travis-ci.org/insideout10/helixware-plugin.svg?branch=master)](https://travis-ci.org/insideout10/helixware-plugin)
[![Code Rewview](https://codeclimate.com/github/insideout10/helixware-plugin.png)](https://codeclimate.com/github/insideout10/helixware-plugin)


HelixWare plugin
================

A plugin to turn WordPress into a video site.

Unit Tests
----------

Install required libraries:

    composer install


If you encounter issues installing `phpunit/selenium` try running this command:

    composer update --prefer-source phpunit/phpunit-selenium

(for more information see https://github.com/giorgiosironi/phpunit-selenium/issues/321#issuecomment-53419629)


Drop any existing test database:

    mysqladmin -u root drop wordpress_test


Install the WordPress test suite running the following command.

    ./bin/install-wp-tests.sh wordpress_test root '' localhost latest

