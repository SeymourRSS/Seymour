# Seymour

[![CI](https://github.com/SeymourRSS/Seymour/actions/workflows/ci.yml/badge.svg)](https://github.com/SeymourRSS/Seymour/actions/workflows/ci.yml)
[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)

An open source RSS reader built on the [TALL Stack](https://tallstack.dev/): Tailwind CSS, Alpine.js, Laravel, Livewire.

See the [Architecture](ARCHITECTURE.md) outline for an overview of the project structure.

## Local Development

To spin up a local development environment you must have [Docker](https://docs.docker.com/get-docker/) and [Docker Compose](https://docs.docker.com/compose/install/) installed on your host machine.

1. Clone this repository to your host machine.
2. Build the docker images: `docker-compose build`
3. Install the PHP dependencies: `docker-compose run --rm seymour composer install`
4. Install the Node dependencies: `docker-compose run --rm node npm install`
5. Build the assets: `docker-compose run --rm node npm run local`
6. Create a folder for local storage: `mkdir -p storage/app`
7. Create the database file: `touch storage/app/seymour.sqlite`
8. Add `seymour.test` to your hosts file: `sudo bash -c "echo '127.0.0.1 seymour.test' >> /etc/hosts"`
9. Spin up the docker services: `docker-compose up -d`
10. Run the migrations: `docker-compose seymour php artisan migrate`

You should now be able to visit `seymour.test` with your browser.  If you see a certificate warning you may need to add `seymour.test` as an exception; the LDE does not have an SSL certificate.

## Special Thanks:

- [Laravel-Lang](https://github.com/Laravel-Lang/lang): an excellent resource for language files.
