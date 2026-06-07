1. docker compose up -d
2. docker ps -a (check if it is running)
3. docker compose stop/docker compose start
4. docker compose down (to remove the process)

run app

1. php -S 0.0.0.0:8000 -t public

migrations

0. Open php.ini and uncomment extension=pdo_mysql, extension=openssl, as well as extension_dir = "ext"
1. php src/Database/migrate.php


admin
php promote.php <username> — промотира потребител до admin.
php promote.php demote <username> — сваля до обикновен потреби