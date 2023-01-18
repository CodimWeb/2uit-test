up: docker-up
down: docker-down
#restart: docker-down docker-up
#init: docker-down docker-pull docker-build docker-up other-init

docker-up:
	docker-compose up -d
	docker-compose run --rm php composer install
	docker-compose run php php artisan migrate
	docker-compose run php php artisan storage:link

	docker-compose run --rm node npm install
	docker-compose run --rm node npm run build

docker-down:
	docker-compose down
