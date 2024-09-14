# php-express
PHP micro-framework inspired by Express.js

| Build | GitHub pages | Stable | License |
| --- | --- | --- | --- |
| [![CI](https://github.com/riverside/php-express/actions/workflows/test.yml/badge.svg)](https://github.com/riverside/php-express/actions/workflows/test.yml) | [![pages-build-deployment](https://github.com/riverside/php-express/actions/workflows/pages/pages-build-deployment/badge.svg)](https://github.com/riverside/php-express/actions/workflows/pages/pages-build-deployment) | [![Latest Stable Version](https://poser.pugx.org/riverside/php-express/v/stable)](https://packagist.org/packages/riverside/php-express) | [![License](https://poser.pugx.org/riverside/php-express/license)](https://packagist.org/packages/riverside/php-express) |

### Requirements
- PHP >= 7.1
- PHP extensions:
  - JSON (`ext-json`)

### Installation
If Composer is not installed on your system yet, you may go ahead and install it using this command line:
```
$ curl -sS https://getcomposer.org/installer | php
```
Next, add the following require entry to the <code>composer.json</code> file in the root of your project.
```json
{
    "require" : {
        "riverside/php-express" : "^2.0"
    }
}
```
Finally, use Composer to install php-express and its dependencies:
```
$ php composer.phar install 
```
### Routing
```php
<?php
$app = new \Riverside\Express\Application();

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

$app->get('about', function ($req, $res) {
    $res->send('about');
});

$app->get('random.text', function ($req, $res) {
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

#### Router
```php
<?php
$router = new \Riverside\Express\Router($app);

$router->param('uuid', '[a-f\d]{8}-[a-f\d]{4}-[a-f\d]{4}-[a-f\d]{4}-[a-f\d]{12}');

$router->get('/', function ($req, $res) {
    $res->send('Birds home page');
});

$router->get('about', function ($req, $res) {
    $res->send('About birds');
});

$router->get('ticket/:uuid/', function($req, $res) {
    echo $req->params['uuid'];
});

$router->run();
```
#### Middleware
```php
$app->use(function($req, $res) {
    $res->header('X-Frame-Options', 'DENY');
    $res->header('X-Powered-By', false);
});

$app->use('/cors', function($req, $res) {
    $res->header('Access-Control-Allow-Origin', '*');
});
```