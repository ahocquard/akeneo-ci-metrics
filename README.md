# Akeneo CI metrics

This programs collects and consolidates metrics about builds run on the Continuous Integration of Akeneo.

The main goals are:
- detect improvements and regressions on the CI, by displaying the duration of the builds over time
- improve our suite of tests, by identifying the most time consuming tests

It uses [Grafana](https://grafana.com/) to display the metrics.

## Requirements

* PHP >= 7.1
* composer

## How it works?

The goal of this PHP script is to gather information about builds and tests by requesting [Jenkins Blue Ocean API](https://github.com/jenkinsci/blueocean-plugin/tree/master/blueocean-rest).
All the information is saved in a dedicated Mysql database.

Then, Grafana display the needed information with dashboards. These dashboards execute directly SQL requests on the database.

## Installation for development

- Install [Docker Engine](https://docs.docker.com/engine/installation/)
- Install [Docker Compose](https://docs.docker.com/compose/install/)
- Clone this repository and `cd` into it.
- `composer install`


Then, you can easily setup your environment by using `docker-composer`.
At first, you need a Github user account, having access to the CI, with a token.
It is strongly advised to create a dedicated token with read-only access. This token is only used for OAuth 2.0 authentication to the CI.
 
Then:
 
```
$ export DATABASE_URL="mysql://user:password@127.0.0.1:3306/akeneo_ci_metrics" \
JENKINS_API_URL=https://ci.akeneo.com/blue/rest/organizations/jenkins/pipelines/akeneo/ \
GITHUB_USER=user \
GITHUB_TOKEN=token \
APP_ENV=prod \
APP_DEBUG=0 \
APP_SECRET=ahFeeva9

$ docker-compose up
```

Do note that you can use `.env.dist` as well instead of exporting variables when developing.

- Create the tables in the Mysql database.

```
$ export APP_ENV=prod && bin/console akeneo:import:test-metrics
$ export APP_ENV=test && bin/console akeneo:import:test-metrics
```

## Importing metrics data

```
$ export APP_ENV=prod && bin/console akeneo:import:test-metrics
$ export APP_ENV=prod && bin/console akeneo:import:run-metrics
```

Initialisation can take a lot of time (several hours), particularly when importing test metrics from the nightly builds.

## Configuring Grafana

Grafana is accessible at `http://localhost:3000` with the default Docker configuration.
You can [import dashboard](http://docs.grafana.org/reference/export_import/) from the directory `grafana/dashboard` directly into Grafana.

You will have to configure the Mysql Datasource.

## Running tests

Specifications:
```
vendor/bin/phpspec run
```

Integration tests:
```
vendor/bin/simple-phpunit
```
