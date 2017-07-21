FROM php:5-fpm
ENV LANG en_GB.UTF-8

RUN mkdir /web
RUN mkdir /web/maengron
WORKDIR /web/maengron

COPY setup/crond.txt /etc/crontab
COPY . /web/maengron
RUN rm -rf /web/maengron/setup

RUN cd /tmp && curl -sL https://deb.nodesource.com/setup_6.x | bash -

RUN docker-php-source extract && \
    apt-get update && \
    apt-get install -y \
            libmagickwand-dev \
            nodejs \
            git \
            cron \
            gettext \
            libmcrypt-dev \
            python --no-install-recommends

RUN docker-php-ext-install \
			mcrypt \
			mysqli \
			zip \
			pdo_mysql \
			gettext \
			bz2 \
			gd

RUN pecl install imagick-beta && docker-php-ext-enable imagick

RUN cd /web/maengron && npm install gulp -g && npm install && gulp --production && npm prune --production

RUN npm uninstall gulp -g && apt-get autoremove -y nodejs git python
VOLUME /web/maengron

CMD ["php-fpm","-F"]