FROM node:lts

# Update the system
RUN apt update -y
RUN apt upgrade -y

# Copy the frontend project
COPY . /app

# Run NPM
WORKDIR /app
RUN npm install

# Build
RUN npm run build

STOPSIGNAL SIGTERM

# Run frontend web server
CMD ["node .output/server/index.mjs"]