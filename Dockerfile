FROM mtahv3/oberd-webapp:0.3

RUN mkdir /var/www/qhl && \
    sed -i 's/^DocumentRoot/#DocumnetRoot/' /etc/apache2/httpd.conf

ADD . /var/www/qhl/

#RUN echo "DocumentRoot /var/www/qhl/public" >> /etc/apache2/httpd.conf
RUN cat /var/www/qhl/docker/apache.conf >> /etc/apache2/httpd.conf

WORKDIR /var/www/qhl/

RUN php /usr/local/bin/composer install --prefer-dist --no-interaction