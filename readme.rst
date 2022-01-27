###################
Overview
###################
Tiny Parcel APIs
Services to store parcel quote requests and calculate estimated delivery prices for customers.

I use CodeIgniter 3.11 because it's not too big and enough for providing all things i need to accomplish this project.

I use MySQL with structure as shown in sql seed file and it runs on the same server.

###################
Demo 
###################
https://dreamylgl.dev/

The credentials for making auth request /login is { "username" : "test1", "password" : "test1"}

Then the reponse's token must be included in all other requests.

###################
Setup 
###################

Download the setup and then config & import MySQL database via the seed tiny_parcel_db.sql

You can use POSTMAN or anything else for simulate frontend.

There is a Postman iport file tiny_parcel.postman_collection.json  

###################
Test the APIs
###################
You can test the APIs by including header Content-Type & Auth-Key with value application/json & "tiny_parcel_auth" in every requests.

And for API except login you must include id & token that you get after successfully login. 

The header keys are 'User-ID' for customer id & 'Authorization' for the token.

List of the API :

[POST] /auth/login  json { "username" : "test1", "password" : "test1"}

[GET] /parcels/:id

[POST] /parcels/

[PUT] /parcels/:id   json { "parcel_name" : "parcel 1", "parcel_weight" : "1", "parcel_volume": "1", "parcel_declared_value":"1000"}

[DELETE] /parcels/:id

[GET] /prices?parcelIds=11,13,20,29,45,22,55,99

[POST] /auth/logout
