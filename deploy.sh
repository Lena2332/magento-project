#!/bin/bash
set -e

getContainerName()
{
   php -r '$output = shell_exec("docker-compose ps -q | xargs docker inspect");
       if (trim($output) === "") { exit(1); }
       foreach (json_decode($output) as $containerInfo) {
           if ($containerInfo->Path === "docker-php-entrypoint") {
               echo ltrim($containerInfo->Name, "/");
               exit(0);
           }
       }' || exit "Docker container not found!"
}

CONTAINER_NAME=$(getContainerName)
echo "Executing deployment in container ${CONTAINER_NAME}..."

docker exec -it ${CONTAINER_NAME} php bin/magento deploy:mode:set default
# Possible pull fails, merge conflicts or changes in the environment files
git pull origin master || exit
docker exec -it ${CONTAINER_NAME} composer install
docker exec -it ${CONTAINER_NAME} php bin/magento setup:upgrade
docker exec -it ${CONTAINER_NAME} php bin/magento setup:di:compile
# `--jobs` not more than number of cores (threads) - 2
docker exec -it ${CONTAINER_NAME} php bin/magento setup:static-content:deploy en_US uk_UA -f -a frontend --theme Magento/luma --jobs=4
docker exec -it ${CONTAINER_NAME} php bin/magento setup:static-content:deploy en_US uk_UA -f -a frontend --theme OlenaK/luma --jobs=4
docker exec -it ${CONTAINER_NAME} php bin/magento setup:static-content:deploy en_US uk_UA -f -a adminhtml --jobs=4
docker exec -it ${CONTAINER_NAME} php bin/magento deploy:mode:set production --skip-compilation

echo "Success!"
