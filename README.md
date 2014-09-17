[![Build Status](https://secure.travis-ci.org/insideout10/helixware-plugin.svg?branch=master)](https://travis-ci.org/insideout10/helixware-plugin)
[![Code Rewview](https://codeclimate.com/github/insideout10/helixware-plugin.png)](https://codeclimate.com/github/insideout10/helixware-plugin)


HelixWare plugin
================

A plugin to turn WordPress into a video site.

Unit Tests
----------

Drop any existing test database:

    mysqladmin -u root drop wordpress_test


Install the WordPress test suite running the following command.

    ./bin/install-wp-tests.sh wordpress_test root '' localhost latest

