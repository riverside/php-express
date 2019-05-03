# php-express

PHP micro-framework inspired by Express.js

### Requirements
- PHP >= 7.0

### Installation
```
$ php composer.phar install 
```
### Routing
```php
<?php
$app = new \PhpExpress\Application();

$app->get('/', function ($req, $res) {
     $res->send('hello world');
});
```
#### Route methods
```php
<?php
// GET method route
$app->get('/', function ($req, $res) {
    $res->send('GET request to the homepage');
});

// POST method route
$app->post('/', function ($req, $res) {
    $res->send('POST request to the homepage');
});
```
#### Route paths
```php
<?php
$app->get('/', function ($req, $res) {
    $res->send('root');
});

$app->get('/about', function ($req, $res) {
    $res->send('about');
});

$app->get('/random.text', function ($req, $res) {
    $res->send('random.text');
});
```

#### Response methods
| Method             | Description                       |
| ------------------ | --------------------------------- |
| $res->end()        | End the response process.         |
| $res->json()       | Send a JSON response.             |
| $res->redirect()   | Redirect a request.               |
| $res->render()     | Render a view template.           |
| $res->send()       | Send a response of various types. |
| $res->sendStatus() | Set the response status code and send its string representation as the response body. |

#### $app->route()
```php
<?php
$app->route('/book')
    ->get(function ($req, $res) {
        $res->send('Get a random book');
    })
    ->post(function ($req, $res) {
        $res->send('Add a book');
    })
    ->put(function ($req, $res) {
        $res->send('Update the book');
    });
```

#### PhpExpress Router
```php
<?php
$router = new \PhpExpress\Router($app);

$router->get('/', function ($req, $res) {
    $res->send('Birds home page');
});

$router->get('/about', function ($req, $res) {
    $res->send('About birds');
});

$router->run();
```