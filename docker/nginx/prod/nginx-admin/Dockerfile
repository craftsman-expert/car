FROM nginx:latest

COPY nginx.conf /etc/nginx/nginx.conf
COPY fastcgi.conf /etc/nginx/fastcgi.conf
COPY vhost.conf /etc/nginx/sites-enabled/vhost.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
