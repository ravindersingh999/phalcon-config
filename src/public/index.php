<?php
// print_r(apache_get_modules());
// echo "<pre>"; print_r($_SERVER); die;
// $_SERVER["REQUEST_URI"] = str_replace("/phalt/","/",$_SERVER["REQUEST_URI"]);
// $_GET["_url"] = "/";
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Config;
use Phalcon\Di;
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;
use Phalcon\Session\Bag as SessionBag;
use Phalcon\Http\Response;
use Phalcon\Http\Response\Cookies;
use Phalcon\Config\ConfigFactory;


$config = new Config([]);

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// Register an autoloader
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
    ]
);

$loader->register();

$container = new FactoryDefault();

$container->set(
    'config',
    function() {
        $fileName = '../app/etc/config.php';
        $factory  = new ConfigFactory();

        return $factory = $factory->newInstance('php', $fileName);
    },
    true
);

$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

$container->set(
    "cookies",
    function () {
        $cookies = new Cookies();
        $cookies->useEncryption(false);
        return $cookies;
    }
);

$container->set(
    'session',
    function () {
        $session = new Manager();
        $files = new Stream(
            [
                'savePath' => '/tmp',
            ]
        );

        $session
            ->setAdapter($files)
            ->start();

        return $session;
    }
);

$container->set(
    'time',
    function() {
        return date('D-M-S, h:m:s');
    }
);

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);

$application = new Application($container);



$container->set(
    'db',
    function () {
        return new Mysql(
            [
                'host'     => $this['config']['db']->host,
                'username' => $this['config']['db']->username,
                'password' => $this['config']['db']->password,
                'dbname'   => $this['config']['db']->dbname,
            ]
        );
    }
);

$container->set(
    'mongo',
    function () {
        $mongo = new MongoClient();

        return $mongo->selectDB('phalt');
    },
    true
);

try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}