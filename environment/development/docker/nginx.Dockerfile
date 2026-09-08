FROM nginx:alpine

RUN echo "fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;" >> /etc/nginx/fastcgi_params \
    && echo "fastcgi_param DEVELOPMENT_SERVER 1;" >> /etc/nginx/fastcgi_params

EXPOSE 80
