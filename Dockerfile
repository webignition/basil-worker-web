FROM php:7.4-fpm-buster

WORKDIR /app

RUN apt-get -qq update && apt-get -qq -y install  \
  librabbitmq-dev \
  libpq-dev \
  libzip-dev \
  zip \
  && docker-php-ext-install \
  pdo_pgsql \
  zip \
  && pecl install amqp \
  && docker-php-ext-enable amqp \
  && apt-get autoremove -y \
  && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN echo "Install composer"
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

RUN echo "Checking platform requirements"
COPY composer.json /app
COPY composer.lock /app
RUN composer check-platform-reqs --ansi

RUN echo "Installing dependencies"
RUN composer install --no-dev --no-scripts
RUN rm composer.lock

RUN echo "Copying source"
COPY bin/console /app/bin/console
RUN chmod +x /app/bin/console
COPY public/index.php public/
COPY src /app/src
COPY config/bundles.php /app/config/
COPY config/services.yaml /app/config/
COPY config/packages/*.yaml /app/config/packages/
COPY config/packages/prod /app/config/packages/prod
COPY config/routes/annotations.yaml /app/config/routes/
RUN touch /app/.env

ARG APP_ENV=prod
ARG DATABASE_URL=postgresql://database_user:database_password@0.0.0.0:5432/database_name?serverVersion=12&charset=utf8
ARG COMPILER_SOURCE_DIRECTORY=/app/source
ARG COMPILER_TARGET_DIRECTORY=/app/tests
ARG MESSENGER_TRANSPORT_DSN=amqp://rabbitmq_user:rabbitmq_password@rabbitmq_host:5672/%2f/messages

ENV APP_ENV=$APP_ENV
ENV DATABASE_URL=$DATABASE_URL
ENV COMPILER_SOURCE_DIRECTORY=$COMPILER_SOURCE_DIRECTORY
ENV COMPILER_TARGET_DIRECTORY=$COMPILER_TARGET_DIRECTORY
ENV MESSENGER_TRANSPORT_DSN=$MESSENGER_TRANSPORT_DSN

RUN echo "Clearing app cache"
RUN php bin/console cache:clear --env=prod
