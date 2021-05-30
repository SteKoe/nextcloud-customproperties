FROM nextcloud

RUN apt-get update && apt-get install -y wget

RUN wget -O /usr/local/bin/phpunit https://phar.phpunit.de/phpunit-9.phar \
    && chmod +x /usr/local/bin/phpunit

WORKDIR /var/www/html/apps/customproperties

ENTRYPOINT ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
