<?php

    namespace RoutesDD;

    /**
     * Class CitiesNetwork - Control, calculate distances and load de paths in the graph
     */
    class CitiesNetwork {

        /** Container for dependency injection */
        private $container;

        /** Path to vertices folder */
        private $filepath;

        /** Graph with weighted paths */
        private $graph;

        /** Cities stored */
        private $cities;

        /** Class constructor */
        public function __construct($container, $filepath) {
            $this->container = $container;
            $this->filepath = $filepath;
            $this->cities = $this->container->get("citiesStore")->findAll(["_id" => "desc"]);
            $this->load();
        }

        /** Load the weighted graph from vertices.txt file */
        public function load() {
            $positions = [];
            foreach($this->cities as $city) {
                $positions[$city['name']] = [
                    'lat' => $city['lat'], 'long' => $city['long']                    
                ];
                $this->graph[$city['name']] = [];
            }
            $handle = fopen($this->filepath . "/vertices.txt", "r");
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    $pieces = explode(",", $line);
                    $source = trim($pieces[0]);
                    $target = trim($pieces[1]);
                    if(array_key_exists($source, $positions) && 
                        array_key_exists($target, $positions)) {
                        $distance = $this->haversine(
                            $positions[$source]['lat'], $positions[$source]['long'],
                            $positions[$target]['lat'], $positions[$target]['long']
                        );
                        $this->graph[$source][$target] = $distance;
                        $this->graph[$target][$source] = $distance;
                    }
                }
            }
        }

        /** Return the stored cities */
        public function getCities() {
            return $this->cities;
        }
        
        /** Return the weighted graph */
        public function getGraph() {
            return $this->graph;
        }

        /** Calculate the distance in km between two geographic points */
        private function haversine($lat1, $long1, $lat2, $long2) {
            $earth_radius = 6371;
            $dLat = deg2rad($lat2 - $lat1);
            $dLon = deg2rad($long2 - $long1);
            $handle = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
            return  $earth_radius * (2 * asin(sqrt($handle)));
        }

    }