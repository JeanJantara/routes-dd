<?php

    namespace RoutesDD;

    /**
     * Class Dijkstra - Use dijkstra algorithm to calcule the shortest path
     */
    class Dijkstra {

        /** Graph with weighted paths */
        private $graph;

        /** Class constructor */
        public function __construct($graph) {
            $this->setGraph($graph);
        }

        /** Set the graph */
        public function setGraph($graph) {
            $this->graph = $graph;
        }

        /** Calculate the shortest path using dijkstra and return a array with every distance */
        public function shortest($init, $end) {
            if ($end === $init) {
                return [$init => 0];
            }
            $distances = array_fill_keys(array_keys($this->graph), PHP_INT_MAX);
            $distances[$init] = 0;
            $prev = [];
            $queue = [$init => 0];
            while (!empty($queue)) {
                $closest = array_search(min($queue), $queue);
                if (!empty($this->graph[$closest])) {
                    foreach ($this->graph[$closest] as $neigh => $cost) {
                        if (isset($distances[$neigh])) {
                            if ($distances[$closest] + $cost < $distances[$neigh]) {
                                $distances[$neigh] = $distances[$closest] + $cost;
                                $queue[$neigh] = $this->distances[$neigh];
                                $prev[$neigh] = [$closest, $cost];
                            }
                        }
                    }
                }
                unset($queue[$closest]);
            }
            if (empty($prev[$end])) {
                return [];
            } else {
                $result = [];
                $current = $end;
                while($current != $init) {
                    $result[] = [$prev[$current][0] => $prev[$current][1]];
                    $current = $prev[$current][0];
                }
                $result = array_reverse($result);
                $result[] = [$end => 0];
                return $result;
            }
        }

    }