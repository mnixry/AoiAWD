FROM node:6 as frontend
COPY ./Frontend /usr/src/Frontend
WORKDIR /usr/src/Frontend
RUN npm install && \
    npm run build

FROM php:7-cli
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY ./AoiAWD /usr/src/AoiAWD
WORKDIR /usr/src/AoiAWD
RUN pecl install mongodb && \
    docker-php-ext-enable mongodb && \
    mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" && \
    echo "phar.readonly=Off" > "$PHP_INI_DIR/conf.d/phar.ini" && \
    rm -rf ./src/public/static/* 

COPY --from=frontend /usr/src/Frontend/dist/* ./src/public/static/
RUN cd ./src && \
    composer install --no-dev --no-interaction --no-progress --no-suggest --optimize-autoloader  &&\
    mv ./public/static/index.html ./public/index.html 

EXPOSE 1337 8023
ENTRYPOINT [ "php", "./src/main.php" ]