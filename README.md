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

### Usage
##### Setup
Firstly, you need to download plugin. Source files are based in Auth_v2 folder. So you should copy this folder into your project and place these lines into youк code:
```php
// Define relative path to plugin
// It is temporary
define("__AUTH_REFERANCE__", "Auth_v2/");

// Get plugin instance
$auth = require_once 'Auth_v2/Base.php';
```

##### Login

##### Lockscreen

##### Log out

##### Using roles

##### Multiple connections

##### IP list

### Support
