FROM node:lts-bullseye as build
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM nginx:alpine
COPY --from=build /app/dist /var/www/app/
COPY default.conf /etc/nginx/conf.d/default.conf  
EXPOSE 5173
CMD ["nginx", "-g", "daemon off;"]


# docker run --rm -d -p 5173:5173 frontend