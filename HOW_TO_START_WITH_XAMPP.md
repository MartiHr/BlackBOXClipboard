1. Pravilen .env

Eto tova sa pravilnite stojnosti za da trygne v XAMPP

-------------------------------------------
MYSQL_ROOT_PASSWORD=
MYSQL_DATABASE=blackbox_db_2
MYSQL_USER=root
MYSQL_PASSWORD=

MYSQL_PORT=3306

# Base path when deployed in a subfolder (e.g. /w26/viewer360-fn1-fn2-fn3). Leave empty for root deployment.
BASE_PATH=/wc26/project12/BlackBOXClipboard

# SMTP configuration for sending emails
SMTP_HOST=smtp.resend.com
SMTP_PORT=587
SMTP_USER=resend
SMTP_PASS=re_aSdC4XEU_8nf1hZ8qzs5dZYgMTKrcg3g5
MAIL_FROM=onboarding@resend.dev
-------------------------------------------

2. Start na MySQL ot XAMPP Control Panel
3. Izpylnqvame tova: C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS blackbox_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
4. Puskame migraciite taka: php C:\xampp\htdocs\wc26\project12\BlackBOXClipboard\src\Database\migrate.php
5. Da razkomentirame v C:\xampp\php\php.ini:
Open it in a text editor and find these lines (they're commented out with ;):
;extension=pdo_mysql
;extension=openssl
;extension_dir = "ext"

6. Puskame apache-to ot XAMPP Control Panel-a
7. Dostypvame na http://localhost/wc26/project12/BlackBOXClipboard/