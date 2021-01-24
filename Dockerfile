FROM amazon/aws-lambda-provided:al2 as base

# DEST (destination) build arg to build for aws or local dev
ARG DEST

# Install the official AWS tools.
# Install Extra Packages for Enterprice Linux
RUN yum install -y amazon-linux-extras
RUN amazon-linux-extras install epel -y

# Update the package database
# To search for packages go to http://rpms.remirepo.net/
RUN rpm -Uvh http://rpms.remirepo.net/enterprise/remi-release-7.rpm

# Install PHP CLi and composer with the remi-php74 repo enabled
RUN yum --enablerepo=remi-php74 install -y \
    php-cli-7.4.14 \
    composer

# Install XDebug if DEST build arg is local
# Use bash conditional to prevent install on each build unless required
RUN if [[ "$DEST" == "local" ]] ; then \
        yum --enablerepo=remi-php74 install -y \
        php-xdebug ; \
    fi

# Copy the bootstrap file to /var/runtime and grant it executable rights
COPY bootstrap /var/runtime
RUN chmod 755 /var/runtime/bootstrap
# Copy a php.ini override to /etc/php.d
COPY 99-prod-overrides.ini /etc/php.d
# Copy application code to /var/task
COPY handler.php composer.json /var/task/
COPY src /var/task/src
# Install dependencies via composer
# Use bash conditional to prevent install on each build unless
RUN if [[ "$DEST" == "aws" ]] ; then \
        composer install --no-ansi --no-dev --no-interaction --no-progress --prefer-dist --no-scripts --optimize-autoloader ; \
    fi

# remove composer
RUN yum remove -y composer

# Tell the lambda what value to give the $_HANDLER env var.
CMD [ "handler.php" ]
