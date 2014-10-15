Hyperion API
============

See the Hyperion Workflow documentation for more details on the general operation of the Hyperion platform.

Diagrams
--------

* [Release Process](https://www.lucidchart.com/documents/edit/4aa78fb8-abf8-45a7-85c1-796e0c6ba1e4/0)
* [ERD](https://www.lucidchart.com/documents/edit/365ed83b-415e-486f-a4a7-3d3a9acb21d9/0)
* [Workflow](https://www.lucidchart.com/documents/edit/5a1a820b-7293-4fb3-b670-f9c9b4ab6e00/0)


Setup
=====
Application Dependencies
------------------------

### Composer

    # Linux:
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin

    # Windows:
    # https://getcomposer.org/download/

### Node: Minifiers

    # Ubuntu:
    apt-get -y --purge remove node nodejs nodejs-legacy
    apt-get -y install nodejs-legacy npm

    # Windows:
    # http://nodejs.org/dist/npm/

    # Once node is installed:
    npm install -g uglifycss uglify-js

    # If you are having proxy issues with NPM:
    #npm config set proxy http://localhost:3128
    #npm config set https-proxy http://localhost:3128
    #npm config set registry "http://registry.npmjs.org/"

### Ruby: Sass & Compass

    # Ubuntu:
    apt-get -y install ruby

    # Windows:
    # http://rubyinstaller.org/

    # Once Ruby is installed:
    gem install sass compass


Vhost Install
-------------
A sample vhost file for Apache is available in the docs folder.

Config
------
See [Config](docs/Config.md) for user and database configuration, including PDO session setup.

Quirks
------
See [Quirks](docs/Quirks.md) for some common causes of unexpected issues.
