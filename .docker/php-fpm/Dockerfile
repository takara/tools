FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
	pdo 
RUN apt-get install -y vim git
RUN apt-get install -y unzip
ADD key.tar.gz /root/.ssh/
ADD composer /usr/local/bin/
RUN mkdir /var/www/tools
WORKDIR /var/www/tools
RUN set -ex && apt-get -y update && apt-get -y install $PHPIZE_DEPS \
      git \
      vim \
      zip \
      unzip \
      patch \
      bash-completion \
      libicu-dev \
      default-mysql-client \
      libldap2-dev \
      imagemagick \
      libmagickwand-dev \
      libyaml-dev \
      libonig-dev \
      libzip-dev \
      openssh-client \
      procps

RUN set -ex && apt-get -y install tzdata && rm -f /etc/localtime && cp /usr/share/zoneinfo/Asia/Tokyo /etc/localtime && apt-get -y remove tzdata

RUN apt-get -y install openssh-server redis sudo net-tools dnsutils inetutils-ping

RUN docker-php-ext-install -j$(nproc) intl \
    pdo_mysql \
    mbstring \
    exif \
    opcache \
    ldap \
    shmop \
    bcmath \
    sockets \
    gettext \
    sysvmsg \
    sysvsem \
    sysvshm

RUN yes '' | pecl install apcu \
    igbinary \
    redis \
    xdebug-2.9.8 \
    YAML

RUN docker-php-ext-install \
    zip 
RUN usermod -u 501 -s /bin/bash www-data
COPY docker-php-entrypoint /usr/local/bin/
ADD key.tar.gz /etc/ssh
COPY authorized_keys /var/www/.ssh/
RUN chown -R www-data /var/www/.ssh/ && \
	chmod -R og-rwx /var/www/.ssh/