# eg_assignment_invoice
## pre-requisite 
* Install docker

## Project setup
* After cloning the project run the following for 1st time
  * `docker-compose up -d --build` this will take time for the first time and later on run the only this `docker-compose up -d`
  
*  Create a `.env` file and add the following in the same 
   ```

     ###> symfony/framework-bundle ### 
        APP_ENV=dev
        APP_SECRET=14865c4acdde4c1037a5d08068b7a1e0
     ###< symfony/framework-bundle ### 
    
    ###> doctrine/doctrine-bundle ###
        DATABASE_URL="mysql://user:user@eg-assignment2-sql:3306/EGAssignment"
    ###< doctrine/doctrine-bundle ###
    
   
   ```
* Now run docker container using this `docker exec -it eg-assignment2-php-fpm bash`
* Once inside the docker run `composer install`   
* Update the DB by using the following
    ```
        php bin/console make:migration
        php bin/console doctrine:migrations:migrate
    ```
  OR 
  ```
    php bin/console doctrine:schema:update
  ```
* Now you can run the API as given below
    ```
       Create Invoice
       URL: http://localhost:8080/api/v1/invoices
       Method: POST
       Request Payload: {
                             "amount": 199.99,
                             "due_date": "2021-09-11"
                         } 
    ```
    ```
       Get All Invoice
       URL: http://localhost:8080/api/v1/invoices
       Method: GET
    ```
    ```
     Create Invoice
     URL: http://localhost:8080/api/v1/invoices/{invoiceId}/payments
     Method: POST
     Request Payload: {
                           "amount": 19,
                       } 
    ```
    ```
     Create Invoice
     URL: http://localhost:8080/api/v1/invoices/process-overdue
     Method: POST
     Request Payload: {
                          "late_fee": 10.5,
                          "overdue_days": 10
                      }
    ```
