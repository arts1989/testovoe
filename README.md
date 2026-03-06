## Зависимости

- PHP 8.3 (FPM)
- Symfony 7.4
- RabbitMQ 3 (AMQP)
- Nginx
- Docker Compose

## Запуск

```bash
# Запуск
docker compose build
docker compose up -d

# Подтянуть vendor
docker exec php composer install
```

**Запросы:**

```bash
curl -X POST http://localhost:8080/api/logs/ingest \
  -H "Content-Type: application/json" \
  -d '{
    "logs": [
      {
        "timestamp": "2026-02-26T10:30:45Z",
        "level": "error",
        "service": "auth-service",
        "message": "User authentication failed",
        "context": {
          "user_id": 123,
          "ip": "192.168.1.1",
          "error_code": "INVALID_TOKEN"
        },
        "trace_id": "abc123def456"
      },
      {
        "timestamp": "2026-02-26T10:30:46Z",
        "level": "info",
        "service": "api-gateway",
        "message": "Request processed",
        "context": {
          "endpoint": "/api/users",
          "method": "GET",
          "response_time_ms": 145
        },
        "trace_id": "abc123def456"
      }
    ]
  }'
```

**202 Accepted**

```json
{
  "status": "accepted",
  "batch_id": "batch_550e8400e29b41d4a716446655440000",
  "logs_count": 2
}
```

**Ошибка**

```bash
curl -X POST http://localhost:8080/api/logs/ingest \
  -H "Content-Type: application/json" \
  -d '{
    "logs": [
      {
        "level": "error",
        "service": "auth-service"
      }
    ]
  }'
```

**400 Bad Request**

```bash
curl -X POST http://localhost:8080/api/logs/ingest \
  -H "Content-Type: application/json" \
  -d '{"logs": []}'
```

## Тесты

```bash
# Запуск всех тестов
docker exec php php bin/phpunit

```