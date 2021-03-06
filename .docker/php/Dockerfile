FROM php:7.2.3-fpm

##<romaricp>##
RUN apt-get update \
    && apt-get install -y --no-install-recommends vim curl debconf subversion git apt-transport-https apt-utils \
    build-essential locales acl mailutils wget nodejs zip unzip \
    gnupg gnupg1 gnupg2 libxslt-dev

RUN docker-php-ext-install pdo pdo_mysql bcmath xsl

COPY php.ini /usr/local/etc/php/php.ini
COPY php-fpm-pool.conf /etc/php/7.2.3/pool.d/www.conf

### INSTALL COMPOSER ###
RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
	mv composer.phar /usr/local/bin/composer

### INSTALL PHPUNIT ###
RUN wget --no-check-certificate https://phar.phpunit.de/phpunit-6.5.3.phar && \
    mv phpunit*.phar phpunit.phar && \
    chmod +x phpunit.phar && \
    mv phpunit.phar /usr/local/bin/phpunit

RUN	echo "deb https://deb.nodesource.com/node_9.x jessie main" >> /etc/apt/sources.list.d/nodejs.list && \
	wget -nv -O -  https://deb.nodesource.com/gpgkey/nodesource.gpg.key | apt-key add - && \
	echo "deb-src https://deb.nodesource.com/node_9.x jessie main" >> /etc/apt/sources.list.d/nodejs.list && \
	curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - && \
	echo "deb https://dl.yarnpkg.com/debian/ stable main" >> /etc/apt/sources.list.d/yarn.list && \
	apt-get update && \
	apt-get install -y --force-yes nodejs yarn && \
	rm -f /etc/apt/sources.list.d/nodejs.list

### INSTALL YARN - REQUIRED BY WEBPACK ###
RUN	yarn add sass-loader@^7.0.1 node-sass --dev

RUN groupadd dev -g 999
RUN useradd dev -g dev -d /home/dev -m

RUN rm -rf /var/lib/apt/lists/*
RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
    echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen && \
    locale-gen

### INSTALL WKHTML - PDF GENERATOR ###
RUN wget https://github.com/wkhtmltopdf/wkhtmltopdf/releases/download/0.12.3/wkhtmltox-0.12.3_linux-generic-amd64.tar.xz
RUN tar vxf wkhtmltox-0.12.3_linux-generic-amd64.tar.xz
RUN cp wkhtmltox/bin/wk* /usr/local/bin/

### INSTALL PHPDOX ###
RUN wget http://phpdox.de/releases/phpdox.phar
RUN chmod +x phpdox.phar
RUN mv phpdox.phar /usr/local/bin/phpdox
RUN phpdox --version

### INSTALL DEPLOYER ###
RUN curl -LO https://deployer.org/deployer.phar
RUN mv deployer.phar /usr/local/bin/dep
RUN chmod +x /usr/local/bin/dep

### INSTALL PHP-CS-FIXER ###
RUN wget https://cs.symfony.com/download/php-cs-fixer-v2.phar -O php-cs-fixer
RUN chmod a+x php-cs-fixer
RUN mv php-cs-fixer /usr/local/bin/php-cs-fixer

### RUN DOCKER ENTRYPOINT WITH ALL INITIALIZE PROCESS ##
COPY docker-php-entrypoint.sh /usr/local/bin/docker-php-entrypoint
RUN chmod +x /usr/local/bin/docker-php-entrypoint

WORKDIR /home/wwwroot/s4bs/
##</romaricp>##

ENTRYPOINT ["docker-php-entrypoint"]

EXPOSE 9000
CMD ["php-fpm"]
