# TODO Shunter

This TODO app is a working example for [shunter][nature/shunter].

## Installation

### Local

The application needs PHP (> 5.4) and sqlite3 running in your machine.

The installation could be done via [composer][getcomposer.org].
 - Clone the repo and run: `composer install`
 - Run the db migrations: `vendor/bin/phinx migrate`
 - Start a php server: `php -S 0.0.0.0:8080`
 - Access `localhost:8080?json=true` in your browser

### Vagrant
 - TODO

### Docker
 - TODO

## Shunter Setup
 - TODO [how to connect shunter with this app]

[nature/shunter]: https://github.com/nature/shunter
[getcomposer.org]: https://getcomposer.org/
