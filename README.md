# Lumen User CRUD API using Multiple Database Connection

This is an example of CRUD API implementation using multiple database and *Repository Design Pattern*. A small set of unit test examples are also included.

### Prerequisite things to do
- Create a ```.env``` file in the root directory using the example file ```.env.example```
- Setup your Firebase Project and MongoDB Database, then put it in the ```.env```

### Used Database Connection
- Firebase Firestore ```firestore```
- MongoDB ```mongodb```

### API End Points
There are five endpoints in this repo to perform CRUD operation :
- **Get All Users** - Retrieve all Users from database
**GET** ```api-url.test/api/v1/{database}/user```
Example Response :
```json
{
    "error": false,
    "message": "Success",
    "data": [
        {
            "id": "dc21e1ad-30cb-4b72-89ab-63823e104a10",
            "name": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
            "email": "Updated (again) User Email",
            "created_at": "2020-12-15 22:51:10",
            "updated_at": "2020-12-15 22:51:28"
        }
    ]
}
```
- **Get a User by ID** - Retrieve a single User from database using its ID
**GET** ```api-url.test/api/v1/{database}/user/{id}```
Example Response :
```json
{
    "error": false,
    "message": "Success",
    "data": {
        "id": "dc21e1ad-30cb-4b72-89ab-63823e104a10",
        "name": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
        "email": "Updated (again) User Email",
        "created_at": "2020-12-15 22:51:10",
        "updated_at": "2020-12-15 22:51:28"
    }
}
```
- **Create a New User**
**User** ```api-url.test/api/v1/{database}/user```
Request Body (JSON) :
```json
{
    "name": "User Title",
    "email": "Lorem ipsum dolor sit amet."
}
```
Example Response :
```json
{
    "error": false,
    "message": "A User has been created",
    "data": {
        "id": "fd0eac71-44cf-41c7-a9fb-adc505bb7416",
        "name": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
        "email": "User Email",
        "created_at": "2020-12-16 01:50:15",
        "updated_at": "2020-12-16 01:50:15"
    }
}
```
- **Update an Existing User** - Update a User using its ID
**PUT** ```api-url.test/api/v1/{database}/user/{id}```
Request Body (JSON) :
```json
{
    "name": "User Name Updated",
    "email": "User Email Updated."
}
```
Example Response :
```json
{
    "error": false,
    "message": "User updated",
    "data": {
        "id": "ed458fac-7910-49ee-9cba-e7f76df9c74b",
        "name": "User Body Updated.",
        "email": "User Email Updated",
        "created_at": "2020-12-16 01:50:58",
        "updated_at": "2020-12-16 01:51:33"
    }
}
```
- **Delete an Existing User** Delete a User using its ID
**DELETE** ```api-url.test/api/v1/{database}/user/{id}```
Example Response :
```json
{
    "error": false,
    "message": "User deleted"
}
```

The ```{database}``` should be replaced with corresponding database connection available. For example if you're about to use **Firebase Firestore** to get all the Users, then the API end point becomes ```api-url.test/api/v1/firestore/user```

### Unit Test
Run this command to test **Get All Users API**, **Get a User by ID API**, and **Create a New User API**
```sh
$ vendor/bin/phpunit
```
### Bonus(?)
**Email Send** in ```app/Controllers/CommunicationController.php``` that can be accessed at **POST** ```api-url.test/api/v1/email/send```
Request Body (JSON) :
```json
{
    "from": "Email origin name",
    "to": "Email destination name",
    "subject": "Subject Email",
    "text": "Body Email"
}
```

**Reqres API Login** that can be accessed at 
**POST** ```api-url.test/api/v1/reqres/login```
Request Body (JSON) :
```json
{
    "email": "Email origin name",
    "passwor": "Email destination name"
}
```
**Reqres API Register** that can be accessed at **POST** ```api-url.test/api/v1/reqres/register```
Request Body (JSON) :
```json
{
    "email": "Email origin name",
    "passwor": "Email destination name"
}
```