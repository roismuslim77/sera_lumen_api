FROM php:7.3-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip 

WORKDIR /var/www/html

# Install MS ODBC Driver for SQL Server
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - \
    && curl https://packages.microsoft.com/config/debian/9/prod.list > /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && apt-get -y --no-install-recommends install msodbcsql17 unixodbc-dev \
    && pecl install sqlsrv \
    && pecl install pdo_sqlsrv \
    && echo "extension=pdo_sqlsrv.so" >> `php --ini | grep "Scan for additional .ini files" | sed -e "s|.*:\s*||"`/30-pdo_sqlsrv.ini \
    && echo "extension=sqlsrv.so" >> `php --ini | grep "Scan for additional .ini files" | sed -e "s|.*:\s*||"`/30-sqlsrv.ini \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/* \
    && pecl install redis

RUN apt-get update \
    && apt-get install -y --no-install-recommends openssl libssl-dev libcurl4-openssl-dev \
    && pecl install mongodb \
    && cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini \
    && echo "extension=mongodb.so" >> /usr/local/etc/php/php.ini \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-enable redis