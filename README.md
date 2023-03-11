# Новый АИС

# Авторизация

Авторизация с ролями с помощью jwt токенов.
У каждого пользователя есть username,телефон,пароль,id роли.
Логин пока делается через username.
Пароли хранятся в бд в виде хэша.
Пользователю при логине выдаются access и refresh токены,в них зашифрована информация,которая опознается сервером как безопасная,за счет подписи внутри этого токена.Т.е. с помощью токенов сервер может безопасно узнавать данные об авторизованном пользователе.
Access токен нужен для доступа к роутам,т.е. без этого токена в заголовке Authorization к http запросу сервер выдаст оформленную ошибку. Если токен истек или неправильный,то сервер также оформленную ошибку.
Refresh токен нужен для обновления пары токенов во время пользования роутами,т.е. если у пользователя во время пользования сайтом заканчивается access токен,то посылается запрос на /refresh с access токеном и после этого со свежим токеном повторяется запрос.
Для пользователя не причиняется никаких неудобств,при этом сохраняется безопасность от использования токенов.
У роутов для работы с информацией о пользователях и ролями есть middleware проверяющий,что запрос пришел от авторизованного админа как раз по access токену.

# Routes

Блок роутов:
    МЕТОД:
        URL/{динамичный параметр}:
            -HEADERS
            -BODY
            -Response

Авторизация/Регистрация:
    POST:

        /login:
            Заголовки:
                Content-type:  application/json

            Тело запроса:
                {
	                "username":"test",
	                "password":"123"
                }

            Ответ запроса:
                {
                    "user": {
                        "username": "test2",
                        "phone": "+7924234234234r3referferf",
                        "userId": 3
                    },
                    "tokens": {
                        "accessToken": "какой-то токен",
                        "refreshToken": "какой-то токен"
                    }
                }

        /registration:
            Заголовки:
                Content-type:  application/json
            
            Тело запроса:
                {
	                "username":"test2",
	                "phone":"+7924234234234r3referferf",
	                "password":"123"
                }

            Ответ запроса:
                {
                    "user": {
                        "username": "test2",
                        "phone": "+7924234234234r3referferf",
                        "userId": 3
                    },
                    "tokens": {
                        "accessToken": "какой-то токен",
                        "refreshToken": "какой-то токен"
                    }
                }
            

        /refresh:
            Заголовки:
                Content-type:  application/json

            Тело запроса:
                {
	                "refreshToken":"какой-то токен"
                }

            Ответ запроса:
                {
                    "accessToken": "какой-то токен",
                    "refreshToken": "какой-то токен"
                }

Работа с ролями:
    GET:

        /roles/all:
            Заголовки:
                Authorization:Bearer аксессТокен

            Тело запроса:
                Отсутствует

            Ответ запроса:
                [
                    {
                        "id": 2,
                        "name": "admin"
                    },
                    {
                        "id": 1,
                        "name": "user"
                    }
                ]

        /roles/one/{roleId}:
            Заголовки:
                Authorization:Bearer аксессТокен

            Тело запроса:
                Отсутствует

            Ответ запроса:
                {
                    "id": 2,
                    "name": "admin"
                }
    POST:
        /roles/create:
            Заголовки:
                Content-type:  application/json
                Authorization:Bearer аксессТокен

            Тело запроса:
                {
                    "name": "testRole"
                }

            Ответ запроса:
                Отсутствует

    PUT:
        /roles/update/{roleId}:
            Заголовки:
                Content-type:  application/json
                Authorization:Bearer аксессТокен

            Тело запроса:
                {
                    "name": "testRole"
                }

            Ответ запроса:
                Количество обновленных строк в БД

    DELETE:
        /roles/update/{roleId}:
            Заголовки:
                Authorization:Bearer аксессТокен

            Тело запроса:
                Отсутствует

            Ответ запроса:
                Количество удаленных строк в БД

Работа с пользователями:
    GET:
        /users/all:
            Заголовки:
                Отсутствуют

            Тело запроса:
                Отсутвует

            Ответ запроса:
                [
                    {
                        "id": 2,
                        "username": "test",
                        "phone": "+7924234234234r3",
                        "password": "$2y$10$K8HxFyz\/BU88NCz.rFgvsOucoNsfD3qa98MGP4lafBw\/v2nBmYwtC",
                        "created_at": "2023-03-11 10:18:33",
                        "updated_at": "2023-03-11 10:18:33"
                    },
                    {
                        "id": 3,
                        "username": "test2",
                        "phone": "+7924234234234r3referferf",
                        "password": "$2y$10$YU9avBNNRfgOy6YpcDJfW.B\/QZRVFAEMefTT1J09IaCg\/KOzrK1Va",
                        "created_at": "2023-03-11 10:39:42",
                        "updated_at": "2023-03-11 10:39:42"
                    }
                ]

        /users/one/{userId}:
            Заголовки:
                Отсутствуют

            Тело запроса:
                Отсутвует

            Ответ запроса:
                {
                    "id": 2,
                    "username": "test",
                    "phone": "+7924234234234r3",
                    "password": "$2y$10$K8HxFyz\/BU88NCz.rFgvsOucoNsfD3qa98MGP4lafBw\/v2nBmYwtC",
                    "created_at": "2023-03-11 10:18:33",
                    "updated_at": "2023-03-11 10:18:33"
                }

    POST:

        /users/add-role/{userId}/{roleId}
            Заголовки:
                Authorization:Bearer аксессТокен

            Тело запроса:
                Отсутвует

            Ответ запроса:
                Отсутвует

    DELETE:

        /users/delete/{userId}
            Заголовки:
                Authorization:Bearer аксессТокен

            Тело запроса:
                Отсутвует

            Ответ запроса:
                Количество удаленных строк в БД

        /users/delete-role/{userId}/{roleId}
            Заголовки:
                Authorization:Bearer аксессТокен

            Тело запроса:
                Отсутвует

            Ответ запроса:
                Количество удаленных строк в БД

        
   

# Тех.описание

Бэкэнд авторизации написан на Laravel 9.19 PHP 8.0.2,полный список пакетов лежит в:
    -Для PHP в composer.json
    -Для JS в package.json

Таблицы в БД:
    -users
    -roles
    -users_roles

/database/migration - папка с миграциями БД
/database/seeders - папка с сидерами БД

/routes/api.php - файл с описанием всех ссылок

/app/Http/Controller - папка с контроллерами,контроллеры работают с http запросом(обрабатывают url запроса,его заголовки,метод запроса и т.д.),на основании информации из http запроса передают параметры в сервисы,сервисы эти данные как-то обрабатывают и возвращают результат.
    -AuthController.php
    -RoleController.php
    -UserController.php

/app/Http/Middlewares - папка с мидлварами,мидлвары-это функции,которые срабатывают перед тем,как в роуте сработает контроллер,по вызванному url.
Пока есть только один мидлвар adminOnly,который по токену,который лежит в заголовке Authorization,проверяет,есть у пользователя роль админа (id роли админа в таблице roles - 2),если есть,то позволяет роуту сработать и отдать данные,если нет,то возвращает оформленную ошибку.
    -adminOnly.php

/app/Models - папка с моделями laravel
    -Role.php
    -User.php

/app/Services - папка с сервисами,сервисы - это классы для работы с информацией,полученной от контроллера.Это может быть работа с БД(обработка ошибок БД,получение из БД,удаление,внесение и т.д),работа с токенами(создание токенов,их валидация) и т.д.
    -AuthService.php
    -JwtService.php
    -RoleService.php
    -UserService.php




