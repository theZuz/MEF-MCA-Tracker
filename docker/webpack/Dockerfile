FROM node:alpine
WORKDIR /var/www/html

COPY package.json \
	package-lock.json \
	./
RUN yarn install

COPY assets /var/www/html/assets
COPY webpack.config.js \
	./

#RUN yarn run webpack
CMD ["yarn", "run", "webpack"]
