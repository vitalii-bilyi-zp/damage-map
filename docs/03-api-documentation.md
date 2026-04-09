# Документація REST API системи "Damage Map"

## 1. Загальні відомості

### 1.1. Базова URL-адреса

```
{BASE_URL}/api
```

### 1.2. Формат даних

- **Content-Type:** `application/json`
- **Кодування:** UTF-8

### 1.3. Формат відповіді

Всі відповіді API мають стандартизований формат (бібліотека F9Web\ApiResponseHelpers):

**Успішна відповідь:**
```json
{
    "data": { ... },
    "message": "Success"
}
```

**Помилка:**
```json
{
    "message": "Error message",
    "errors": { ... }
}
```

### 1.4. HTTP статус-коди

| Код | Опис |
|-----|------|
| 200 | Успішний запит |
| 201 | Ресурс створено |
| 400 | Невірний запит |
| 401 | Не автентифіковано |
| 403 | Доступ заборонено |
| 404 | Ресурс не знайдено |
| 422 | Помилка валідації |
| 500 | Внутрішня помилка сервера |
| 502 | Помилка зовнішнього сервісу |

## 2. Автентифікація та авторизація

### 2.1. Механізм автентифікації

Система використовує **Laravel Sanctum** з Bearer Token автентифікацією.

**Заголовок авторизації:**
```
Authorization: Bearer {access_token}
```

### 2.2. Отримання токену

#### POST /api/login

Автентифікація користувача та отримання токену доступу.

**Тіло запиту:**
```json
{
    "email": "user@example.com",
    "password": "password123"
}
```

**Успішна відповідь (200):**
```json
{
    "data": {
        "user": {
            "id": 1,
            "name": "Іван Петренко",
            "email": "user@example.com",
            "access_token": "1|abc123xyz...",
            "roles": ["admin"]
        }
    }
}
```

**Помилка автентифікації (401):**
```json
{
    "message": "Invalid credentials"
}
```

### 2.3. Вихід з системи

#### POST /api/logout

**Заголовки:** Потребує автентифікації

**Відповідь (200):**
```json
{
    "message": "Success"
}
```

### 2.4. Поточний користувач

#### GET /api/user

Отримання інформації про автентифікованого користувача.

**Заголовки:** Потребує автентифікації

**Відповідь (200):**
```json
{
    "data": {
        "user": {
            "id": 1,
            "name": "Іван Петренко",
            "email": "user@example.com",
            "region_id": null,
            "district_id": null,
            "community_id": null,
            "roles": ["super_admin"]
        }
    }
}
```

## 3. Довідники

### 3.1. Регіони

#### GET /api/regions

Отримання списку регіонів України.

**Параметри запиту:**

| Параметр | Тип | Опис |
|----------|-----|------|
| load_details | boolean | Завантажити райони та громади |

**Відповідь (200):**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Харківська",
            "districts": [
                {
                    "id": 1,
                    "name": "Харківський",
                    "communities": [
                        {
                            "id": 1,
                            "name": "Харківська міська"
                        }
                    ]
                }
            ]
        }
    ]
}
```

### 3.2. Громади

#### GET /api/communities

Отримання списку територіальних громад.

**Відповідь (200):**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Харківська міська",
            "district_id": 1
        }
    ]
}
```

### 3.3. Типи об'єктів

#### GET /api/object-types

Отримання списку типів об'єктів з категоріями.

**Відповідь (200):**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Житловий будинок",
            "object_category_id": 1,
            "object_category": {
                "id": 1,
                "name": "Житлова нерухомість"
            }
        }
    ]
}
```

### 3.4. Типи ремонту

#### GET /api/repair-types

Отримання списку типів ремонту.

**Відповідь (200):**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Поточний ремонт"
        },
        {
            "id": 2,
            "name": "Капітальний ремонт"
        },
        {
            "id": 3,
            "name": "Реконструкція"
        }
    ]
}
```

## 4. Записи про пошкодження (Damage Notes)

### 4.1. Дані для карти

#### GET /api/damage-notes/regions

Агреговані дані пошкоджень по регіонах.

**Параметри запиту:**

| Параметр | Тип | Опис |
|----------|-----|------|
| object_type_id | integer | Фільтр за типом об'єкта |
| object_category_id | integer | Фільтр за категорією |

**Відповідь (200):**
```json
{
    "data": [
        {
            "name": "Харківська",
            "restoration_cost": "125000000.00"
        },
        {
            "name": "Донецька",
            "restoration_cost": "340000000.00"
        }
    ]
}
```

#### GET /api/damage-notes/districts

Агреговані дані пошкоджень по районах.

**Параметри запиту:** Аналогічно `/regions`

**Відповідь (200):**
```json
{
    "data": [
        {
            "name": "Харківський",
            "restoration_cost": "45000000.00"
        }
    ]
}
```

#### GET /api/damage-notes/communities

Агреговані дані пошкоджень по громадах.

**Параметри запиту:** Аналогічно `/regions`

**Відповідь (200):**
```json
{
    "data": [
        {
            "name": "Харківська міська",
            "restoration_cost": "25000000.00"
        }
    ]
}
```

### 4.2. Списки записів

#### GET /api/damage-notes/approved

Отримання списку схвалених записів.

**Заголовки:** Потребує автентифікації

**Відповідь (200):**
```json
{
    "data": [
        {
            "id": 1,
            "date": "2024-01-15",
            "community": "Харківська міська",
            "object_type": "Житловий будинок",
            "city": "Харків",
            "street": "Сумська",
            "building_number": "25",
            "floors": 5,
            "area": "2500.00",
            "damage_type": "medium",
            "restoration_cost": "1500000.00",
            "predicted_restoration_cost": "1450000.00",
            "comment": "Пошкодження даху та фасаду"
        }
    ]
}
```

#### GET /api/damage-notes/not-approved

Отримання списку записів на розгляді.

**Заголовки:** Потребує автентифікації

**Відповідь (200):**
```json
{
    "data": [
        {
            "id": 2,
            "request_id": 5,
            "date": "2024-01-20",
            "community": "Ізюмська міська",
            "object_type": "Школа",
            "city": "Ізюм",
            "street": "Центральна",
            "building_number": "10",
            "damage_type": "high",
            "restoration_cost": "5000000.00"
        }
    ]
}
```

#### GET /api/damage-notes/search

Пошук серед схвалених записів.

**Заголовки:** Потребує автентифікації

**Параметри запиту:**

| Параметр | Тип | Опис |
|----------|-----|------|
| q | string | Пошуковий запит (адреса або громада) |

**Приклад:**
```
GET /api/damage-notes/search?q=Сумська
```

**Відповідь (200):**
```json
{
    "data": [
        {
            "id": 1,
            "community": "Харківська міська",
            "address": "Сумська 25, Харків",
            "date": "2024-01-15"
        }
    ]
}
```

### 4.3. Окремий запис

#### GET /api/damage-notes/{id}

Отримання детальної інформації про запис.

**Заголовки:** Потребує автентифікації

**Відповідь (200):**
```json
{
    "data": {
        "id": 1,
        "date": "2024-01-15",
        "object_type_id": 1,
        "community_id": 1,
        "city": "Харків",
        "street": "Сумська",
        "building_number": "25",
        "floors": 5,
        "area": "2500.00",
        "damage_type": "medium",
        "repair_type_id": 2,
        "restoration_cost": "1500000.00",
        "predicted_restoration_cost": "1450000.00",
        "comment": "Пошкодження даху та фасаду",
        "object_type": {
            "id": 1,
            "name": "Житловий будинок",
            "object_category": {
                "id": 1,
                "name": "Житлова нерухомість"
            }
        },
        "community": {
            "id": 1,
            "name": "Харківська міська",
            "district": {
                "id": 1,
                "name": "Харківський",
                "region": {
                    "id": 1,
                    "name": "Харківська"
                }
            }
        },
        "repair_type": {
            "id": 2,
            "name": "Капітальний ремонт"
        },
        "damage_note_request": {
            "id": 1,
            "full_name": "Петренко І.І.",
            "email": "petrenko@example.com",
            "phone": "+380501234567",
            "approved_at": "2024-01-16 10:30:00"
        },
        "damage_note_images": [
            {
                "id": 1,
                "file_path": "/storage/damage_note_images/abc123.jpg"
            }
        ]
    }
}
```

### 4.4. Редагування запису

#### PUT /api/damage-notes/{id}

Оновлення запису про пошкодження.

**Заголовки:** Потребує автентифікації (роль: admin, super_admin)

**Тіло запиту:**
```json
{
    "date": "2024-01-15",
    "object_type_id": 1,
    "community_id": 1,
    "city": "Харків",
    "street": "Сумська",
    "building_number": "25",
    "floors": 5,
    "area": 2500,
    "damage_type": "medium",
    "repair_type_id": 2,
    "restoration_cost": 1600000,
    "comment": "Оновлений коментар"
}
```

**Примітка:** При зміні ключових параметрів (object_type_id, community_id, repair_type_id, damage_type, area, floors) система автоматично перераховує прогнозовану вартість через ML-сервіс.

**Відповідь (200):**
```json
{
    "message": "Success"
}
```

**Помилка ML-сервісу (502):**
```json
{
    "message": "Flask request failed",
    "errors": {
        "status": 500,
        "flask_error": { ... }
    }
}
```

### 4.5. Видалення запису

#### DELETE /api/damage-notes/{id}

Видалення запису про пошкодження.

**Заголовки:** Потребує автентифікації (роль: admin, super_admin)

**Відповідь (200):**
```json
{
    "message": "Success"
}
```

### 4.6. Імпорт з файлу

#### POST /api/damage-notes/import-file

Імпорт записів з Excel файлу.

**Заголовки:** Потребує автентифікації (роль: super_admin)

**Content-Type:** `multipart/form-data`

**Параметри:**

| Параметр | Тип | Опис |
|----------|-----|------|
| file | file | Excel файл (.xlsx, .xls) |

**Формат файлу (колонки):**
1. Дата (YYYY-MM-DD)
2. Тип об'єкта (назва)
3. Громада (назва)
4. Місто
5. Вулиця
6. Номер будинку
7. Ступінь пошкодження (Легке/Середнє/Тяжке)
8. Вартість відновлення
9. Коментар

**Відповідь (200):**
```json
{
    "message": "Success"
}
```

### 4.7. Експорт у CSV

#### GET /api/damage-notes/export-csv

Експорт записів у CSV файл.

**Заголовки:** Потребує автентифікації

**Відповідь:** CSV файл з заголовками:
- ID, Date, Object type, Region, District, Community, City, Street, Building number, Damage type, Restoration cost, Comment

## 5. Заявки на внесення даних (Damage Note Requests)

### 5.1. Створення заявки

#### POST /api/damage-note-requests

Створення нової заявки з даними про пошкодження.

**Публічний endpoint** (не потребує автентифікації)

**Тіло запиту:**
```json
{
    "full_name": "Петренко Іван Іванович",
    "email": "petrenko@example.com",
    "phone": "+380501234567",
    "date": "2024-01-20",
    "object_type_id": 1,
    "community_id": 1,
    "city": "Харків",
    "street": "Полтавський шлях",
    "building_number": "15",
    "floors": 3,
    "area": 450,
    "damage_type": "medium",
    "repair_type_id": 1,
    "restoration_cost": 850000,
    "comment": "Пошкодження вікон та фасаду"
}
```

**Відповідь (200):**
```json
{
    "message": "Success"
}
```

### 5.2. Схвалення заявки

#### POST /api/damage-note-requests/{id}/approve

Схвалення заявки на внесення даних.

**Заголовки:** Потребує автентифікації (роль: admin, super_admin)

**Відповідь (200):**
```json
{
    "message": "Success"
}
```

### 5.3. Відхилення заявки

#### POST /api/damage-note-requests/{id}/decline

Відхилення заявки з коментарем.

**Заголовки:** Потребує автентифікації (роль: admin, super_admin)

**Тіло запиту:**
```json
{
    "comment": "Недостатньо інформації для підтвердження"
}
```

**Відповідь (200):**
```json
{
    "message": "Success"
}
```

## 6. Статистика

### 6.1. Глобальна статистика

#### GET /api/statistics/global

Статистика пошкоджень за часовими періодами.

**Параметри запиту:**

| Параметр | Тип | Обов'язковий | Опис |
|----------|-----|--------------|------|
| start_date | date | Так | Початок періоду (YYYY-MM-DD) |
| end_date | date | Так | Кінець періоду (YYYY-MM-DD) |
| period_type | integer | Так | Тип періоду: 0=день, 1=тиждень, 2=місяць |
| dimension_type | string | Ні | Вимір: objects_number (за замовч.) або restoration_cost |
| region_id | integer | Ні | Фільтр за регіоном |

**Приклад:**
```
GET /api/statistics/global?start_date=2024-01-01&end_date=2024-01-31&period_type=0&dimension_type=objects_number
```

**Відповідь (200):**
```json
{
    "data": {
        "January 01, 2024": 5,
        "January 02, 2024": 3,
        "January 03, 2024": null,
        "January 04, 2024": 7
    }
}
```

### 6.2. Статистика співвідношень

#### GET /api/statistics/ratio

Розподіл пошкоджень за типами об'єктів.

**Параметри запиту:**

| Параметр | Тип | Обов'язковий | Опис |
|----------|-----|--------------|------|
| start_date | date | Так | Початок періоду |
| end_date | date | Так | Кінець періоду |
| dimension_type | string | Ні | objects_number або restoration_cost |
| object_category_id | integer | Ні | Фільтр за категорією |
| region_id | integer | Ні | Фільтр за регіоном |

**Відповідь (200):**
```json
{
    "data": {
        "Житловий будинок": 45,
        "Школа": 12,
        "Лікарня": 8,
        "Адміністративна будівля": 15
    }
}
```

### 6.3. OLAP-куб

#### GET /api/statistics/cube

Багатовимірний аналіз даних.

**Параметри запиту:**

| Параметр | Тип | Обов'язковий | Опис |
|----------|-----|--------------|------|
| start_date | date | Ні | Початок періоду |
| end_date | date | Ні | Кінець періоду |
| dimension_type | string | Так | Тип виміру (див. нижче) |

**Типи вимірів:**
- `day` — по днях
- `week` — по тижнях
- `month` — по місяцях
- `object_category` — по категоріях об'єктів
- `object_type` — по типах об'єктів
- `region` — по регіонах
- `district` — по районах
- `community` — по громадах
- `damage_type` — по ступенях пошкодження

**Приклад:**
```
GET /api/statistics/cube?start_date=2024-01-01&end_date=2024-01-31&dimension_type=region
```

**Відповідь (200):**
```json
{
    "data": [
        {
            "title": "Харківська",
            "restoration_cost": "125000000.00",
            "objects_number": 156
        },
        {
            "title": "Донецька",
            "restoration_cost": "340000000.00",
            "objects_number": 423
        }
    ]
}
```

## 7. Прогнозування вартості

### 7.1. Отримання прогнозу

#### POST /api/predict-restoration-cost

Отримання прогнозу вартості відновлення з опціональним коригуванням на інфляцію.

**Заголовки:** Потребує автентифікації

**Тіло запиту:**
```json
{
    "object_type_id": 1,
    "community_id": 1,
    "repair_type_id": 2,
    "damage_type": "medium",
    "area": 2500,
    "floors": 5,
    "work_year": 2026,
    "work_month": 4
}
```

**Параметри запиту:**

| Параметр | Тип | Обов'язковий | Опис |
|----------|-----|--------------|------|
| `object_type_id` | integer | Так | ID типу об'єкта |
| `community_id` | integer | Так | ID громади |
| `repair_type_id` | integer | Так | ID типу ремонту |
| `damage_type` | string | Так | Ступінь пошкодження: `low`, `medium`, `high` |
| `area` | numeric | Так | Площа (м²), 1–1 000 000 |
| `floors` | integer | Так | Кількість поверхів, 1–100 |
| `work_year` | integer | Ні | Рік проведення робіт (2020–2040). Обов'язковий якщо передано `work_month` |
| `work_month` | integer | Ні | Місяць проведення робіт (1–12). Обов'язковий якщо передано `work_year` |

**Примітка:** `work_year` та `work_month` передаються разом або не передаються взагалі. При відсутності — `adjusted_cost == predicted_cost`, `inflation_k == 1.0`.

**Внутрішня передача коефіцієнтів:** Laravel автоматично додає до payload Flask поле `inflation_indices` — повний масив записів з таблиці `inflation_indices` (метод `InflationIndex::getAllForPayload()`, кеш 60 хв). Flask використовує ці значення для розрахунку `inflation_k` замість вбудованого словника. Клієнту передавати це поле не потрібно.

**Відповідь (200):**
```json
{
    "data": {
        "predicted_cost": 1450000.00,
        "adjusted_cost": 1798435.00,
        "inflation_k": 1.2403,
        "base_year": 2024,
        "base_month": 1,
        "work_year": 2026,
        "work_month": 4,
        "currency": "UAH",
        "model": "gradient_boosting_v2",
        "base_value": 1380000.00,
        "contributions": [
            { "group": "area", "percent": 35.2, "contribution": 512640.0 },
            { "group": "damage_level", "percent": 28.1, "contribution": 409560.0 }
        ],
        "pie": {
            "Площа": 35.2,
            "Тип пошкодження": 28.1
        }
    }
}
```

**Помилка сервісу (502):**
```json
{
    "message": "Upstream request failed",
    "errors": {
        "message": "Connection timeout"
    }
}
```

---

### 7.2. Порівняння сценаріїв

#### POST /api/predict-restoration-cost/scenario-comparison

Порівняння прогнозованої вартості для кількох місяців/років (до 72 сценаріїв).

**Заголовки:** Потребує автентифікації

**Тіло запиту:**
```json
{
    "object_type_id": 1,
    "community_id": 1,
    "repair_type_id": 2,
    "damage_type": "medium",
    "area": 2500,
    "floors": 5,
    "periods": [
        { "year": 2026, "month": 4 },
        { "year": 2026, "month": 7 },
        { "year": 2027, "month": 1 },
        { "year": 2027, "month": 6 }
    ]
}
```

**Параметри:**

| Параметр | Тип | Обов'язковий | Опис |
|----------|-----|--------------|------|
| `object_type_id` | integer | Так | ID типу об'єкта |
| `community_id` | integer | Так | ID громади |
| `repair_type_id` | integer | Так | ID типу ремонту |
| `damage_type` | string | Так | Ступінь пошкодження |
| `area` | numeric | Так | Площа (м²) |
| `floors` | integer | Так | Кількість поверхів |
| `periods` | array | Так | Масив `{year, month}`, 1–72 елементів |
| `periods.*.year` | integer | Так | Рік (2020–2040) |
| `periods.*.month` | integer | Так | Місяць (1–12) |

**Відповідь (200):**
```json
{
    "data": {
        "scenarios": [
            {
                "predicted_cost": 1450000.00,
                "adjusted_cost": 1450000.00,
                "inflation_k": 1.0,
                "base_year": 2024,
                "base_month": 1,
                "work_year": 2026,
                "work_month": 4,
                "year": 2026,
                "month": 4,
                "delta_pct": 0
            },
            {
                "predicted_cost": 1450000.00,
                "adjusted_cost": 1595000.00,
                "inflation_k": 1.1,
                "base_year": 2024,
                "base_month": 1,
                "work_year": 2026,
                "work_month": 7,
                "year": 2026,
                "month": 7,
                "delta_pct": 10.0
            }
        ]
    }
}
```

**Поле `delta_pct`:** відсоткова різниця між `adjusted_cost` поточного сценарію та першого сценарію. Перший сценарій завжди має `delta_pct = 0`.

---

### 7.3. Довідник індексів інфляції

#### GET /api/inflation-indices

Отримання таблиці місячних індексів інфляції для відображення у UI та ручного редагування. Ці ж значення Laravel автоматично передає до Flask у кожному запиті прогнозування (поле `inflation_indices` payload).

**Заголовки:** Потребує автентифікації

**Відповідь (200):**
```json
{
    "data": [
        {
            "id": 1,
            "year": 2022,
            "month": 1,
            "index_value": 0.75,
            "source_type": "official",
            "source_description": "Держстат України, форма №621",
            "created_at": "2026-04-04T12:00:00.000000Z",
            "updated_at": "2026-04-04T12:00:00.000000Z"
        }
    ]
}
```

Записи відсортовані за `year ASC`, `month ASC`. Охоплює 2022–2027 (72 записи).

**Значення `source_type`:**

| Значення | Опис |
|----------|------|
| `official` | Офіційні дані Держстату України |
| `forecast` | Прогноз НБУ |
| `extrapolated` | Екстрапольована оцінка |

## 8. Нормативні документи

### 8.1. Список документів

#### GET /api/regulation-documents

Отримання списку нормативних документів.

**Публічний endpoint**

**Відповідь (200):**
```json
{
    "data": [
        {
            "id": 1,
            "title": "Постанова КМУ №123",
            "file_path": "/storage/regulation_documents/abc123.pdf",
            "created_at": "2024-01-10"
        }
    ]
}
```

### 8.2. Завантаження документу

#### POST /api/regulation-documents

Завантаження нового нормативного документу.

**Заголовки:** Потребує автентифікації (роль: super_admin)

**Content-Type:** `multipart/form-data`

**Параметри:**

| Параметр | Тип | Опис |
|----------|-----|------|
| title | string | Назва документу |
| file | file | PDF файл |

**Відповідь (200):**
```json
{
    "message": "Success"
}
```

### 8.3. Видалення документу

#### DELETE /api/regulation-documents/{id}

Видалення нормативного документу.

**Заголовки:** Потребує автентифікації (роль: super_admin)

**Відповідь (200):**
```json
{
    "message": "Success"
}
```

## 9. Віртуальні тури

### 9.1. Список турів

#### GET /api/virtual-tours

Отримання списку віртуальних турів.

**Заголовки:** Потребує автентифікації

**Відповідь (200):**
```json
{
    "data": [
        {
            "id": 1,
            "title": "Відновлення школи №15",
            "description": "Процес відновлення після пошкодження",
            "content": [ ... ],
            "created_at": "2024-01-15"
        }
    ]
}
```

### 9.2. Створення туру

#### POST /api/virtual-tours

Створення нового віртуального туру.

**Заголовки:** Потребує автентифікації (роль: super_admin)

**Тіло запиту:**
```json
{
    "title": "Назва туру",
    "description": "Опис туру",
    "content": [
        {
            "type": "text",
            "data": {
                "text": "Вступний текст"
            }
        },
        {
            "type": "image",
            "data": {
                "url": "/path/to/image.jpg",
                "caption": "Підпис"
            }
        },
        {
            "type": "slider",
            "data": {
                "images": ["/img1.jpg", "/img2.jpg"]
            }
        },
        {
            "type": "3d",
            "data": {
                "model_url": "/path/to/model.glb"
            }
        }
    ]
}
```

**Відповідь (200):**
```json
{
    "message": "Success"
}
```

## 10. Управління користувачами

### 10.1. Список користувачів

#### GET /api/users

Отримання списку користувачів.

**Заголовки:** Потребує автентифікації (роль: super_admin)

**Відповідь (200):**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Адміністратор",
            "email": "admin@example.com",
            "region_id": null,
            "district_id": null,
            "community_id": null,
            "roles": ["super_admin"]
        }
    ]
}
```

### 10.2. Створення користувача

#### POST /api/users

Створення нового користувача.

**Заголовки:** Потребує автентифікації (роль: super_admin)

**Тіло запиту:**
```json
{
    "name": "Новий Користувач",
    "email": "new@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "admin",
    "region_id": 1,
    "district_id": null,
    "community_id": null
}
```

**Відповідь (200):**
```json
{
    "message": "Success"
}
```

### 10.3. Деталі користувача

#### GET /api/users/{id}

Отримання інформації про користувача.

**Заголовки:** Потребує автентифікації

**Відповідь (200):**
```json
{
    "data": {
        "id": 2,
        "name": "Регіональний адмін",
        "email": "regional@example.com",
        "region_id": 1,
        "district_id": null,
        "community_id": null,
        "roles": ["admin"]
    }
}
```

### 10.4. Оновлення користувача

#### PUT /api/users/{id}

Оновлення даних користувача.

**Заголовки:** Потребує автентифікації

**Тіло запиту:**
```json
{
    "name": "Оновлене ім'я",
    "email": "updated@example.com",
    "password": "newpassword",
    "password_confirmation": "newpassword",
    "role": "admin",
    "region_id": 1
}
```

**Відповідь (200):**
```json
{
    "message": "Success"
}
```

### 10.5. Видалення користувача

#### DELETE /api/users/{id}

Видалення користувача (soft delete).

**Заголовки:** Потребує автентифікації (роль: super_admin)

**Відповідь (200):**
```json
{
    "message": "Success"
}
```

### 10.6. Список ролей

#### GET /api/roles

Отримання списку доступних ролей.

**Заголовки:** Потребує автентифікації

**Відповідь (200):**
```json
{
    "data": [
        {
            "id": 1,
            "name": "super_admin"
        },
        {
            "id": 2,
            "name": "admin"
        },
        {
            "id": 3,
            "name": "analyst"
        }
    ]
}
```

## 11. Middleware

### 11.1. Стандартні middleware

| Middleware | Опис |
|------------|------|
| `auth:api` | Перевірка автентифікації через Sanctum |
| `throttle:api` | Обмеження кількості запитів |

### 11.2. Застосування middleware

```php
// Публічні маршрути (без автентифікації)
Route::get('/regions', [RegionsController::class, 'index']);
Route::get('/statistics/global', [StatisticsController::class, 'showGlobal']);
Route::post('/damage-note-requests', [DamageNoteRequestsController::class, 'store']);

// Захищені маршрути
Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/damage-notes/approved', [DamageNotesController::class, 'getApproved']);
    // ...
});
```

## 12. Валідація

### 12.1. Типові правила валідації

**Запис про пошкодження:**
```php
[
    'date' => 'required|date|after_or_equal:2022-02-24|before_or_equal:today',
    'object_type_id' => 'required|exists:object_types,id',
    'community_id' => 'required|exists:communities,id',
    'city' => 'nullable|string|max:255',
    'street' => 'nullable|string|max:255',
    'building_number' => 'nullable|string|max:50',
    'floors' => 'nullable|integer|min:1|max:100',
    'area' => 'nullable|numeric|min:0',
    'damage_type' => 'required|in:low,medium,high',
    'repair_type_id' => 'nullable|exists:repair_types,id',
    'restoration_cost' => 'required|numeric|min:0',
    'comment' => 'nullable|string|max:1000',
]
```

### 12.2. Помилки валідації (422)

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "date": [
            "The date field is required."
        ],
        "damage_type": [
            "The selected damage type is invalid."
        ]
    }
}
```

## 13. Приклади використання

### 13.1. Автентифікація та отримання даних

```javascript
// 1. Логін
const loginResponse = await fetch('/api/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        email: 'user@example.com',
        password: 'password123'
    })
});
const { data: { user } } = await loginResponse.json();
const token = user.access_token;

// 2. Отримання даних з токеном
const damageNotesResponse = await fetch('/api/damage-notes/approved', {
    headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
    }
});
const damageNotes = await damageNotesResponse.json();
```

### 13.2. Подання заявки на внесення даних

```javascript
const response = await fetch('/api/damage-note-requests', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        full_name: 'Іван Петренко',
        email: 'ivan@example.com',
        phone: '+380501234567',
        date: '2024-01-20',
        object_type_id: 1,
        community_id: 1,
        city: 'Харків',
        street: 'Сумська',
        building_number: '25',
        damage_type: 'medium',
        restoration_cost: 500000
    })
});
```

## 14. Висновки

REST API системи "Damage Map" забезпечує:

- **Повноцінний CRUD** для всіх основних сутностей
- **Гнучку систему авторизації** з рольовим доступом
- **Публічні та захищені endpoints** для різних сценаріїв використання
- **Стандартизований формат відповідей** для зручної інтеграції
- **Валідацію вхідних даних** з детальними повідомленнями про помилки
- **Інтеграцію з ML-сервісом** для прогнозування вартості

API відповідає принципам REST та забезпечує надійну основу для функціонування клієнтського SPA-застосунку.
