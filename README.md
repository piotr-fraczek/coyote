# Coyote

[![StyleCI](https://styleci.io/repos/30256872/shield)](https://styleci.io/repos/30256872)
[![Build Status](https://travis-ci.org/adam-boduch/coyote.svg?branch=master)](https://travis-ci.org/adam-boduch/coyote)

Coyote to nazwa systemu obslugujacego serwis 4programmers.net. Obecnie w obludze jest wersja 1.x ktora mamy nadzieje zastapic wersja 2.0 ktora jest w trakcie pisania i bedzie dostepna na githubie jako open source. 

Uwaga! To repozytorium zawiera wersje 2.0-dev ktora absolutnie nie jest wersja koncowa.

## Wymagania

* PHP >= 5.5
    * Moduł GD
    * Moduł mongo
    * Moduł mcrypt
* PostgreSQL >= 9.3
* MongoDB >= 2.7
* composer
* node.js
* npm
* git

### Zalecane

* Redis
* Beanstalkd

## Instalacja

* `apt-get install php5-gd`
* `apt-get install php5-mongo`
* `apt-get install php5-mcrypt`
* `git clone https://github.com/adam-boduch/coyote.git .`
* `cp .env.default .env` (plik .env zawiera konfiguracje bazy danych PostgreSQL oraz MongoDB)
* `psql -c 'create database coyote;' -U postgres`
* `make install` (na produkcji) lub `make install-dev` (bez minifikacji JS oraz CSS)
* `php artisan key:generate` (generowanie unikalnego klucza, który posłuży do szyfrowania danych)

### Problemy podczas instalacji
#### Class 'MongoClient' not found

Czy biblioteka mongo jest zainstalowana? Jeżeli tak to `service php5-fpm restart`

#### Use of undefined constant MCRYPT_RIJNDAEL_128 - assumed 'MCRYPT_RIJNDAEL_128'

Czy biblioteka mcrypt jest zainstalowana? Jeżeli tak to `service php5-fpm restart`

## Uruchomienie

Działanie projektu wymaga zainstalowania serwera HTTP takiego jak Apache czy Nginx. Laravel udostępnia jednak prosty serwer HTTP, który można wykorzystać, aby sprawdzić działanie aplikacji. Aby go uruchomić należy wykonać polecenie: `php artisan serve`

## Aktualizacja projektu

`make update` (na produkcji) lub `make update-dev` (na serwerze deweloperskim)

## Jak mozesz pomoc?

Zachecamy do aktywnego udzialu w rozwoju projektu. Zajrzyj na zakladke *Issues* i zobacz jakie zadanie mozesz zrealizowac. Realizujemy tylko te zadanie ktore jest zaakceptowane i przypisane do wersji 2.0.. 

1. Utworz fork repozytorium
2. Wprowadz zmiany
3. Dodaj pull request
