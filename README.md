# stock-example
Example of using Stock data

A demo payment system applications based on [CakePHP](https://cakephp.org) 3.x.

## Requirements

PHP 7.1+

## Installation

Install Vagrant (for local usage).
Application have all necessary configuration files for Vagrant.

Pull repository to your machine.

If Vagrant not used - install Composer manually.

In project "code" directory run `composer install`

Assign read\write permissions to temp folder `code/tmp`

## Configuration

For connecting to Vagrant SSH use next settings:

    192.168.10.20:22
    user:vagrant
    password:vagrant


Read and edit `config/app.php` and setup the `'EmailTransport'` and any other
configuration relevant for your application.

## Testing

To run unit tests use command `composer test`
