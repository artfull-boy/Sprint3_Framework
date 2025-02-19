<?php

/**
 * Sets the error reporting level.
 *
 * This function configures the error reporting level for the application.
 * It is typically used to set different error reporting levels for development
 * and production environments.
 *
 * @return void
 */
function setReporting()
{
    if (DEVELOPMENT_ENVIRONMENT == true) {
        error_reporting(E_ALL);
        ini_set('display_errors', 'On');
    } else {
        error_reporting(E_ALL);
        ini_set('display_errors', 'Off');
        ini_set('log_errors', 'On');
        ini_set('error_log', ROOT . DS . 'tmp' . DS . 'logs' . DS . 'error.log');
    }
}

/**
 * Function description here.
 *
 * @param mixed $value Description of the parameter $value.
 * 
 * @return mixed Description of the return value.
 */
function stripSlashesDeep($value)
{
    $value = is_array($value) 
        ? array_map('stripSlashesDeep', $value) 
        : stripslashes($value);
    return $value;
}

/**
 * Removes magic quotes from incoming data.
 *
 * This function is used to clean up data that has been automatically escaped
 * by PHP's magic quotes feature. It recursively strips slashes from strings,
 * arrays, and objects to ensure that data is in its original form.
 *
 * @return void
 */
function removeMagicQuotes()
{
        $_GET    = stripSlashesDeep($_GET);
        $_POST   = stripSlashesDeep($_POST);
        $_COOKIE = stripSlashesDeep($_COOKIE);
    
}

/** 
 * Check register globals and remove them
 * 
 * @return void
 */
function unregisterGlobals()
{
    if (ini_get('register_globals')) {
        $array = ['_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES'];
        foreach ($array as $value) {
            foreach ($GLOBALS[$value] as $key => $var) {
                if ($var === $GLOBALS[$key]) {
                        unset($GLOBALS[$key]);
                }
            }
        }
    }
}

/**
 * Parses the URL, determines the controller, action, and query parameters,  
 * then dynamically calls the appropriate controller method.
 *
 * This function takes a URL from `index.php` and extracts:
 * - The **controller name** (first segment of the URL).
 * - The **action name** (second segment of the URL).
 * - The **query string** (remaining URL segments as an array).
 * 
 * It then:
 * 1. Converts the controller name to a class format (`ucwords()`).
 * 2. Creates a new instance of the controller.
 * 3. Calls the specified action method with the query string as parameters.
 *
 * Example URL format: `yoursite.com/controllerName/actionName/queryString`
 * Example:  
 *   URL: `todo.com/items/view/1/first-item`
 *   - Controller: `ItemsController`
 *   - Model: `item` (derived from controller name)
 *   - Action: `view`
 *   - Query String: `["1", "first-item"]`
 *
 * @global string $url The requested URL.
 *
 * @return void
 */
function callHook()
{
    global $url;

    $urlArray = [];
    $urlArray = explode("/", $url);

    $controller = $urlArray[0];
    array_shift($urlArray);
    $action = $urlArray[0];
    array_shift($urlArray);
    $queryString = $urlArray;

    $controllerName = $controller; //items
    $controller = ucwords($controller); //Items
    $model = rtrim($controller, 's'); //Item
    $controller .= 'Controller'; //ItemsController
    $dispatch = new $controller($model, $controllerName, $action);

    if ((int)method_exists($controller, $action)) {
        call_user_func_array([$dispatch,$action], $queryString);
    } else {
        /* Error Generation Code Here */
    }
}

/**
 * Automatically loads required class files when a class is instantiated.
 *
 * This function checks multiple directories for the requested class file and includes it if found.
 * It helps in avoiding manual `require_once` statements for each class.
 *
 * The function searches for class definitions in:
 * 1. The `library` directory (`ROOT/library/`)
 * 2. The `controllers` directory (`ROOT/application/controllers/`)
 * 3. The `models` directory (`ROOT/application/models/`)
 *
 * File naming convention:
 * - Class names are converted to lowercase.
 * - Files must follow the naming pattern `{className}.class.php` (for library classes) 
 *   or `{className}.php` (for controllers and models).
 *
 * Example usage:
 * If `$obj = new User();` is called, this function will attempt to load:
 * - `ROOT/library/user.class.php`
 * - `ROOT/application/controllers/user.php`
 * - `ROOT/application/models/user.php`
 *
 * @param string $className The name of the class being instantiated.
 *
 * @return void
 */
spl_autoload_register(function ($className) {
    if (file_exists(ROOT . DS . 'library' . DS . strtolower($className) . '.class.php')) {
        include_once ROOT . DS . 'library' . DS . strtolower($className) . '.class.php';
    } elseif (file_exists(ROOT . DS . 'application' . DS . 'controllers' . DS . strtolower($className) . '.php')) {
        include_once ROOT . DS . 'application' . DS . 'controllers' . DS . strtolower($className) . '.php';
    } elseif (file_exists(ROOT . DS . 'application' . DS . 'models' . DS . strtolower($className) . '.php')) {
        include_once ROOT . DS . 'application' . DS . 'models' . DS . strtolower($className) . '.php';
    } else {
        /* Error Generation Code Here */
    }
});

setReporting();
removeMagicQuotes();
unregisterGlobals();
callHook();