FROM ubuntu:latest

RUN apt-get update -y | apt-get upgrade -y

# Create directory
RUN mkdir -p /api
RUN mkdir -p /sql

#Install PHP
RUN apt install curl -y
RUN apt install php-cli unzip php-pear php-mbstring php-mysql -y

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#Config API file
COPY ./myApi /api
WORKDIR /api
RUN composer i
