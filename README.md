# Vozovoz API
Система регистрации и учёта пилотов корпорации Deep Space Conquerors.

## Предварительные настройки:
### Docker
Для работы с проектом необходимо установить Docker и Docker Compose.
- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)

### docker-compose
Чтобы запустить проект на определённом порту, необходимо в файле `docker-compose.yml` изменить порт в разделе `ports`:
```yaml
  nginx:
    ports:
      - "8080:80"
```
