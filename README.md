Тестовое задание
================

## Установка
Для удобства установки приложения удобнее будет воспользоваться Vagrant. Для этого необходимо выполнить следующие команды:
```bash
$ cd ~
$ mkdir test_assignment
$ cd test_assignment
$ wget 'https://raw.githubusercontent.com/eugenkyky/4xxi/master/deploy.sh'
$ wget 'https://raw.githubusercontent.com/eugenkyky/4xxi/master/Vagrantfile'
$ vagrant box add hashicorp/precise64
$ vagrant up
```

## Использование
После того, как развернется окружение (код деплоя можно посмотреть в ```deploy.sh```) 
необходимо обратиться к адресу ```http://127.0.0.1:4567/register/``` зарегистрироваться и перейти к ```http://127.0.0.1:4567``` для проверки функционала ТЗ

## Сторонние библиотеки
Использовал дополнительно два бандла: friendsofsymfony/user-bundle и eightpoints/guzzle-bundle. Первый для регистрации/авторизации пользователя, второй для обращения к внешнему сервису.
Так же использовал https://developers.google.com/chart/ для отображения истории общей стоимости портфеля на клиенте.

## Затраченное время
3 часа PSR-0 PSR-1 PSR-2 PSR-4, Symfony Coding Standards  
1 час знакомст с Symfony  
1 час кодил hello world  
1 час чтение документации secutiry  
1 час Настройка FOSuserbundle  
1.5 часа ORM + MYSQL + check login, register  
1 час чтение документации форм  
1 час - знакомство relations + entity  
1 час - знакомство с yahoo api  
15 часов кодил функционал  
3 часа оформление ТЗ  
