# AuthPHP
AuthPHP is a plugin for implementing authorization on the site.

### Requirements
- PHP 5.4
- PDO class (optionally is included)
- MySQL

### Summary
- Simple usage
- Supports multiple connections
- Tracks the user ip on each connection
- Implements "white" and "black" IP list
- Supports user roles on different pages
- Making logs 

### Setup
Setting up the plugin is quite simple as you can see below:
```php
// Define relative path to plugin. It is temporary thing.
define("__AUTH_REFERANCE__", "Auth_v2/");

// Get plugin instance
$auth = require_once 'Auth_v2/Base.php';

// Using buit-in methods for changing configuration
$auth->config(array(...));
$auth->DBconfig(array(...));

// Using config.ini file
// Example is provided in package
$auth->iniConfig('config.ini');

// Connect to database. Plugin is using PDO class
$auth->connect( HOST_NAME, DB_NAME, LOGIN, PASSWORD);
```


##### Login

##### Lockscreen

##### Log out

##### Using roles

##### Multiple connections

##### IP list

### Support
