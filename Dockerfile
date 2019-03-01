# Build this with docker-compose or using following commands
# docker build -t mikkohei13/piipitin:0.1 .
# docker push -t mikkohei13/piipitin:VERSION-NUMBER
# (See version numbers/tags at Docker Hub: LINK-TO-BE-ADDED)

FROM php:7.1-apache

WORKDIR /var/www/

# Todo: perhaps link this so could be modified? Now uses the ini file that was available during image building.
COPY www/config/php.ini /usr/local/etc/php/

# Add tools
RUN apt-get update && \
apt-get -y upgrade && \
apt-get -y install unzip zlib1g-dev nano #git # zip needed for composer

# Add php extensions / modules
RUN docker-php-ext-install zip
#RUN docker-php-ext-install pdo pdo_mysql

# Add composer
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

# Add App
ADD ./www /var/www
WORKDIR /var/www
RUN composer install
RUN chmod a+w /var/www/logs
#RUN composer require slim/slim "^3.0" # runs, but files are not there!?

# Enable mod rewrite
RUN a2enmod rewrite

# Change Apache root to Slim default
#ENV APACHE_DOCUMENT_ROOT /var/www/html/app/public
ENV APACHE_DOCUMENT_ROOT /var/www

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/
