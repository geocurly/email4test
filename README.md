# email4test

Старт приложения
```bash
docker-compose up
```
Для инициализации базы данных
Пароль по умолчанию - email4test
```bash
 psql -h localhost -p 5432 email4test -U email4test -f ./db/migrations/initialization.sql
```
