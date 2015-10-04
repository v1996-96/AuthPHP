# AuthPHP
AuthPHP is a plugin for implementing authorization on a website.

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

### Login
For making the login page you need to write these lines:
```php
if (isset($_POST["login"]) && isset($_POST["password"])){
  $auth->login($_POST["login"], $_POST["pwd"], isset($_POST["remember"]));
}
// If the 'remember' is checked, plugin will create cookie. Otherwise - session.
```

### Lockscreen
For making the lockscreen page you need to write these lines:
```php
if (isset($_POST["pwd"])) {
  $auth->lockscreen($_POST["pwd"], isset($_POST["remember"]));
}
// If the 'remember' is checked, plugin will create cookie. Otherwise - session.
```

### Log out
For implementing logout yut need to call these functions:
```php
// Close curent connection
$auth->logOut();

// Close all open connections for current user
$auth->full_logOut();
```

### Using roles
For implementing role management, you need on each page run command below before checking the login status:
```php
$auth->setAllowedRoles(array(...)); // array(1, 20, 50)
```
So if user's role is not mentioned in specified list, access will be prohibited. If in settings is specified <code>onRoleMismatch</code>, then user will be rerouted to the specified url. But he will not be logged out. Therefore, if it is necessary, you can do it manually.

### Multiple connections
Using multiple connections is quite simple. You just need to switch <code>multiple</code> parameter to <code>true</code>. In this case new token is created each time when user inputs his login and password and gets access to system.

### IP list
If you want to give access to only specified IP, or to block some IP, you should specify which list you want to use in <code>IPList</code> and insert the data in the list. An example is specified below:
```php
$auth->config(array(
  'IPList' => 'black',
  'IPBlackList' => array(
    '192.168.1.100',
    '192.168.20.100'
  )
));
```

### List of configurations
##### Plugin settings
Do not be scared of such a huge set of configurations. It is quite easy to adopt the plugin to your system.

- **makeLog** (*boolean*) <code>Default true</code>:<br> Write logs or not. Log file is located in the plugin directory. You should close access to it by .htaccess<br>
- **hashName** *string* <code>Default 'token'</code>:<br> Defines key for the token stored at the user side.<br>
- **cookiePath** *string* <code>Default '/'</code>:<br> Defines visibility area for cookies<br>
- **authTime** *integer* <code>Default 10800</code>:<br> Time in seconds, during which user could stay logged in.<br>
- **lockDelay** *integer* <code>Default 1200</code>:<br> Time in seconds, after which user will be rerouted to lockscreen (if it is enabled).<br>
- **checkIPToken** *string* <code>Default 'strict'</code>:<br> Possible variants <code>'strict'</code>, <code>'to_lockscreen'</code> and <code>'acceptable'</code>. Defines action, fired when stored and current IP are different.<br>
- **multiple** *boolean* <code>Default true</code>:<br> Enable or disable multiple connections to a single account.<br>
- **onMultiple** *string* <code>Default 'allow'</code>:<br> Action fired when multiple connections are disabled. Possible variants are <code>'allow'</code> and <code>'discard'</code>. If you allow user log in, it will just rewrite token in database. If you do not allow user log into system, error with status 'User is already logged in' will be fired.<br>
- **reroute** *boolean* <code>Default true</code>:<br> Reroute user to specified pages on key points or not.<br>
- **useLockscreen** *boolean* <code>Default true</code>:<br> Use lockscreen in system or not<br>
- **loginPageUrl** *string* <code>Default '/'</code>:<br> Link to the login page relatively to the site corner<br>
- **lockscreenPageUrl** *string* <code>Default '/lockscreen'</code>:<br> Link to the lockscreen page relatively to the site corner<br>
- **successUrl** *string* <code>Default '/dashboard'</code>:<br> Link to the 'success' page relatively to the site corner<br>
- **lockscreenRef** *boolean|string* <code>Default false</code>:<br> Link to the referer page, wrom which user were rerouted to lockscreen.<br>
- **IPList** *string* <code>Default 'black'</code>:<br> Which IP list use 'white' or 'black'.<br>
- **IPWhiteList** *array(int)* <code>Default empty</code>:<br> List of IP that have the access to the system<br>
- **IPBlackList** *array(int)* <code>Default empty</code>:<br> List of prohibited IP.<br>
- **onRoleMismatch** *boolean|string* <code>Default false</code>:<br> Link to the page, to which user will be rerouted in case of role mismatch.

##### Database settings
In the DBconfig are stored table and field names. So you can use your own tables by changing these names in settings.

**Table 'user'** <code>In config: tUserInfo</code>

| Field name | Field type  | In config | Description        |
|------------|-------------|-----------|--------------------|
| id         | int(11) PK  |           | User identificator |
| login      | varchar(36) | fLogin    | User's login       |
| pwd        | varchar(36) | fPassword | User's password    |
| role       | int(11)     | fRole     | User's role        | 

P.S. By switching <code>hashLogin</code> parameter to false, you can use not hashed login (email instead of login).


**Table 'token'** <code>In config: tUserToken</code>

| Field name | Field type  | In config | Description        |
|------------|-------------|-----------|--------------------|
| id         | int(11) PK  |           | Token identificator |
| id_user    | int(11) FK  | fIdUser   | Link to the user |
| token      | varchar(36) | fToken    | Contains token itself |
| time_add   | datetime curr_timestamp | fTokenAdd | Time when user logged in |
| user_ip    | varchar(36) | fTokenIp  | Contains user's IP, which he has had when token were created |
