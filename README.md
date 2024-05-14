# vsUAccess API Client

The `vsUAccess` class provides a PHP interface to interact with the UniFi Access API. It simplifies the process of managing users, doors, and system logs through a straightforward PHP class. This class is intended for developers who need to integrate UniFi Access functionalities within their PHP applications.

## Features

- User management (create, update, retrieve)
- Door operations (list, lock, unlock)
- Retrieve system logs
- Manage user PIN codes

## Requirements

- PHP 7.2 or higher
- cURL extension enabled in PHP
- Valid UniFi Access API credentials

## Installation

To use the `vsUAccess` class in your project, include the `vsUAccess.php` file in your project directory and require it in your PHP script.

```php
require_once 'path/to/vsUAccess.php';
```


## Initialize the API Client
```php
$api = new vsUAccess("https://your-unifi-access-host", 12445, 'your_api_token_here');
```


# Users
## Create a user
```php
$response = $api->createUser('John', 'Doe', '123456', 1625158800, 'john.doe@example.com');
print_r($response);
```

## Update a user
```php
$response = $api->updateUser('user_id', 'Jane', 'Doe', '654321', 1625158800, 'jane.doe@example.com');
print_r($response);
```

## Get user
```php
$response = $api->getUser('user-id');
print_r($response);
```

## View all users
```php
$response = $api->viewUsers();
print_r($response);
```


# Doors
## List all doors
```php
$response = $api->door_list();
print($response);
```

## Unlock a Door
```php
$response = $api->door_unlock('door_id');
print_r($response);
```

## Lock a Door
```php
$response = $api->door_lock('door_id');
print_r($response);
```

