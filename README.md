# Example App

A **Parking** app for demonstration purposes.

## Technologies used

-   **MongoDB** for storing cars
-   **MySQL** for stоring drivers and the vehicle categories they can drive
-   **Redis** for caching MongoDB aggregations
-   **Elasticsearch** for indexing cars and searching them
-   **Laravel Telescope** for easier local development monitoring
    (requests, dumps, cache)
-   **Laravel Nova** for managing model entries easily

## Project Setup

1. Copy the **.env.project** contents into **.env** file
    > cp .env.example .env
2. Run the sail environment
    > ./vendor/bin/sail build
    > ./vendor/bin/sail up -d
3. Run the migrations:
    > ./vendor/bin/sail artisan migrate
4. Generate a key:
    > ./vendor/bin/sail artisan key:generate
5. Install all dependencies from composer
    > ./vendor/bin/sail composer install

## Functionality

-   Register a car in the parking lot: **(POST)**
    > http://localhost/api/register
    -   registrationPlate
    -   brand
    -   model
    -   color
    -   category **(A,B or C)**
    -   card (Silver, Gold or Platinum)
-   Unregister a car in the parking lot: **(POST)**
    > <http://localhost/api/exit>
    -   registrationPlate
-   See available parking slots: **(GET)**
    > <http://localhost/api/available>
-   Check the current sum of a vehicle in the parking lot: **(GET)**
    > <http://localhost/api/check/{registrationPlate}>
-   Check the number of unique cars entered the parking lot for a period: **(GET)**
    > <http://localhost/api/check/cars/unique>
    -   dateStart **(String)**
    -   (_optional_) dateEnd **(String)**
-   Check the amount of money earned in a period: **(GET)**
    > <http://localhost/api/check/cars/sum>
    -   dateStart **(String)**
    -   (_optional_) dateEnd **(String)**

## Jobs and Workers

-   send:aggregations - Checks the amount of money and number
    of cars entered the parking lot for
-   elastic:index - Indexes all the cars in elasticsearch
