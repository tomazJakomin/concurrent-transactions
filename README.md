# Symfony application using docker

A [Docker](https://www.docker.com/)-based installer and runtime for the [Symfony](https://symfony.com) web framework,
with [FrankenPHP](https://frankenphp.dev) and [Caddy](https://caddyserver.com/) inside!

![CI](https://github.com/dunglas/symfony-docker/workflows/CI/badge.svg)

## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --no-cache` to build fresh images
3. Run `docker compose up -d` to set up and start a fresh Symfony project
4. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
5. Run `docker compose down --remove-orphans` to stop the Docker containers.



## Concurrent transaction

I have implemented locking mechanism on user table before updating 
the balance and creating new transaction.
I did implement locking only on Users table since I lock
specific users row based on id and then when I have that lock
I can ensure no one is updating that user and related transaction.

### Added retry mechanism

There is a while loop to retry locking if the resource is being locked already.
This way I can ensure that updating user balance and adding new different transaction
can be processed succsefully after the lock has been released.


## Further improvements

1. Cover with tests
2. Add api docs
3. Add api authentication
4. Improve validations
5. Add Makefile
6. Adjust logger
