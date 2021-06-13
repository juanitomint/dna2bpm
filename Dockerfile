FROM juanitomint/php7
WORKDIR /var/www/html
COPY --chown=www-data:www-data . /var/www/html/
# RUN chown www-data:www-data . -R