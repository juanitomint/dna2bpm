FROM juanitomint/php7
EXPOSE 80
# CONSUME BUILD ARGS FOR TRACE
ARG VCS_REF
ARG BUILD_DATE
ARG GIT_USER
ARG GIT_USER_EMAIL
LABEL org.label-schema.vcs-ref=$VCS_REF \
      org.label-schema.build-date=$BUILD_DATE \
      org.label-schema.git_user=$GIT_USER \
      org.label-schema.git_user_email=$GIT_USER_EMAIL
      
WORKDIR /var/www/html
COPY --chown=www-data:www-data . /var/www/html/
# RUN chown www-data:www-data . -R