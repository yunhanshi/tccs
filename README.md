# Transformative coding challenge solution
This is a coding challenge solution for Transformative, it's base [Laravel](https://laravel.com/), [Vue.js](https://github.com/vuejs/vue) and the UI Toolkit [Element](https://github.com/ElemeFE/element). 

## Installing
#### Manual

```bash
# Clone the project
git clone https://github.com/yunhanshi/tccs.git

cd tccs

# Copy .env
cp .env.example .env

# Run composer
composer install

# Migration and DB seeder (after changing your DB settings in .env)
php artisan migrate --seed

# Install key
php artisan key:generate

# Install dependency with NPM
npm install

# develop
npm run dev # or npm run watch

# Build on production
npm run production
```

#### Docker
```sh
docker-compose up -d
```
Build static files within Laravel container with npm
```sh
# Get laravel docker container ID from containers list
docker ps

docker exec -it <container ID> npm run dev # or npm run watch
# Where <container ID> is the "laravel" container name, ex: src_laravel_1
```
Open http://localhost:8000 (laravel container port declared in `docker-compose.yml`) to access tccs

## Running the tests
```sh
./vendor/bin/phpunit --testsuite=Unit
```