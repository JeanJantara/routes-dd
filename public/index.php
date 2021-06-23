
<?php

    /** Default constants to manage the paths */
    defined('APP_ROOT') ?: define('APP_ROOT', __DIR__);
    defined('DS') ?: define('DS', DIRECTORY_SEPARATOR);
    defined('ROOT') ?: define('ROOT', dirname(__DIR__) . DS);

    /** Vendor libs */
    require __DIR__ . '/../vendor/autoload.php';

    use DI\Container;
    use Slim\Factory\AppFactory;
    use Slim\Views\TwigMiddleware;

    /** Load the container for dependency injection */
    $container = new Container();
    AppFactory::setContainer($container);

    /** Create the Slim application */
    $app = AppFactory::create();

    /** Set the dependencies */
    $container = require ROOT . 'src/container.php';
    $container($app);

    /** Load the middlewares */
    $app->addRoutingMiddleware();
    $app->addErrorMiddleware(true, true, true);
    $app->add(TwigMiddleware::createFromContainer($app));

    /** Load http routes */
    $routes = require ROOT . 'src/routes.php';
    $routes($app);

    /** Run the application */
    $app->run();