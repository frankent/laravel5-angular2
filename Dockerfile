FROM keittirat/nds-php7:debian-mongo-node
ENV LANG en_GB.UTF-8

RUN mkdir /web
RUN mkdir /web/maengron
WORKDIR /web/maengron

COPY setup/crond.txt /etc/crontab
COPY . /web/maengron
RUN rm -rf /web/maengron/setup

RUN npm install gulp -g
RUN npm install

RUN gulp --production

RUN npm prune --production

RUN npm uninstall gulp -g && apt-get autoremove -y nodejs git python
VOLUME /web/maengron

CMD ["php-fpm","-F"]