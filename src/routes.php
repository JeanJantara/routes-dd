<?php

    use Slim\App;
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;

    return function (App $app) {

        /** Route to homepage */
        $app->get('/', function (Request $request, Response $response, $args) {
            $network = $this->get("citiesNetwork");
            return $this->get('view')->render($response, 'home.twig', [
                'cities' => $network->getCities()
            ]);
        });

        /** Route to create cities */
        $app->post('/city/new', function (Request $request, Response $response, $args) {
            $body = $request->getParsedBody();
            $v = $this->get("validator");
            $v->key('name', $v::stringType())
                ->key('lat', $v::numericVal())
                ->key('long', $v::numericVal());
            if($v->validate($body)) {
                $citiesStore = $this->get("citiesStore");
                $queryBuilder = $citiesStore->createQueryBuilder()->where(["name", "=", $body['name']]);
                $query = $queryBuilder->getQuery();
                if(!$query->exists()) {                    
                    if($city = $this->get("citiesStore")->insert($body)) {
                        $payload = [
                            'error' => false,
                            'msg' => 'Successfully created city.',
                            'city' => $city
                        ];
                    } else {
                        $payload = [
                            'error' => true,
                            'msg' => 'Error creating city.'
                        ];
                    }
                } else {
                    $payload = [
                        'error' => true,
                        'msg' => 'City already exists.'
                    ];
                }
            } else {
                $payload = [
                    'error' => true,
                    'msg' => 'Invalid Data.'
                ];
            }
            $response->getBody()->write(json_encode($payload));
            return $response->withHeader('Content-Type', 'application/json');
        });
        
        /** Route to calculate the shortest path */
        $app->post('/shortest-path', function (Request $request, Response $response, $args) {
            $v = $this->get("validator");
            $v->key('source', $v::stringType())
                ->key('target', $v::stringType());
            $body = $request->getParsedBody();
            if($v->validate($body)) {
                $graph = $this->get("citiesNetwork")->getGraph();
                if(isset($graph[$body['source']]) && isset($graph[$body['target']])) {
                    $dijkstra = $this->get("dijkstra");
                    $shortest = $dijkstra->shortest($body['source'], $body['target']);
                    if(count($shortest) > 1) {
                        $payload = [
                            'error' => false,
                            'msg' => '',
                            'path' => $shortest
                        ];
                    } else if(count($shortest) == 1) {
                        $payload = [
                            'error' => true,
                            'msg' => 'Target equals source.'
                        ];
                    } else if(count($shortest) == 0) {
                        $payload = [
                            'error' => true,
                            'msg' => 'No path found.'
                        ];
                    }
                } else {
                    $payload = [
                        'error' => true,
                        'msg' => 'City not found.'
                    ];
                }
            } else {
                $payload = [
                    'error' => true,
                    'msg' => 'Invalid Entry.'
                ];
            }
            $response->getBody()->write(json_encode($payload));
            return $response->withHeader('Content-Type', 'application/json');
        });

    };