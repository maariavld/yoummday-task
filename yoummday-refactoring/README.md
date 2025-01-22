# ❗ Please do not fork this repository ❗

# Yoummday Refactoring Task
This project only includes the route `GET /has_permission/{token}` which has to decide if the provided token exists and has the required permission.
Your task is to refactor the endpoint and create tests, if necessary.

# Requirements
- php 8.1
- composer

# Installation
```shell
$ composer install
```

# Run
```shell 
$ php src/main.php
```
Expected output: 
```shell
[INFO] Registering GET /has_permission/{token}
[INFO] Server running on 127.0.0.1:1337
```

# Testing
```shell
$ php vendor/bin/phpunit Test
```
