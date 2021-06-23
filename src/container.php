<?php

    use RoutesDD\CitiesNetwork;
    use RoutesDD\Dijkstra;
    use Slim\App;
    use Slim\Views\Twig;
    use Respect\Validation\Validator;

    return function (App $app) {
        
        /** Dependency injection */
        $container = $app->getContainer();

        /** Database object to manipulate the stored cities */
        $container->set('citiesStore', function() {
            $dbDirectory = ROOT . "/database";
            $config = [ "auto_cache" => false ];
            return new SleekDB\Store("cities", $dbDirectory, $config);
        });

        /** Object to control, load and calculate the distances in the cities network */
        $container->set('citiesNetwork', function(\Psr\Container\ContainerInterface $container) {
            $verticesDirectory = ROOT . "/resources";
            return new CitiesNetwork($container, $verticesDirectory);
        });

        /** Object to calculate the shortest path in the cities network */
        $container->set('dijkstra', function(\Psr\Container\ContainerInterface $container) {
            return new Dijkstra($container->get("citiesNetwork")->getGraph());
        });
      
        /** Object to render the views */
        $container->set("view", function() {
            return Twig::create(ROOT . '/templates', 
                ['cache' => false]
            );
        });

        /** Validator object */
        $container->set("validator", function() {
            return Validator::create();
        });

    };