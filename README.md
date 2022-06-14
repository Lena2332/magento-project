## Project Deployment ##
After running the `composer install` command revert the following files:

```bash
# ./pub/.htaccess - checkout due to websites mapping and custom rewrite rules
git checkout ./pub/.htaccess
```
# Local development #

Create a symlink to the proper `app/etc/env.*.php` file. It is preferable to use `app/etc/env.dev.php` as the
configuration source. Use this with the appropriate `docker-compose*.yaml` file.


# Deploying changes to the server #

IMPORTANT! Check `git status` before running any deployment scripts!

### Local testing before commit ###

The code must be tested before commit. Change the `./scenario/full-deploy.yml`:
- set `configuration > notification > enabled` to `false`;
- set `deployment > database > backup` to `false`;
- remove `git pull {branch}` command from the scenario file because it can't be executed inside the Docker container;
- remove `--no-dev` from the `composer install`.

```shell
docker exec -it $(getContainerName) php mad.phar deployment:run full-deploy --php-binary=php --composer=composer
```

### Deploy changes (any server except production) ###

```shell
php mad.phar deployment:run full-deploy --branch="origin development" --environment=development
```

Deployment **process flow** implemented in the above files:

1) set the website to `maintenance` (only `full-deploy` scenario);
2) switch to the `default` mode;
3) pull changes from the **current** Git branch;
4) install modules;
5) run `setup:upgrade`;
6) compile the code and assets;
7) switch to the `production` mode without compilation;
8) set proper indexer modes;
9) turn off maintenance (only `full-deploy` scenario).

### Deploy to production ###

Full deploy to production consists of two stages:
- full deploy on the build system;
- pull changes and sync prepared files to production.

```shell
# Deploy to the build system
cd ~/domains/dv-campus-2021-2022-magento-local.allbugs.info/build_system/
php mad.phar deployment:run full-deploy --environment=build
# Pull changes and deploy to the production system
cd ~/domains/dv-campus-2021-2022-magento-local.allbugs.info/http/
php mad.phar deployment:run production-deploy --environment=production
```
