# build stage
FROM node:lts-alpine as build-stage
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# production stage
FROM php:8.2-apache
RUN docker-php-ext-install mysqli
COPY --from=build-stage /dist /var/www/html/
EXPOSE 80/tcp