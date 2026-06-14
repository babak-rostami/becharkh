<?php

try {
    $manager = new MongoDB\Driver\Manager("mongodb://127.0.0.1:27017");
    $command = new MongoDB\Driver\Command(["ping" => 1]);
    $cursor = $manager->executeCommand("admin", $command);

    echo "MongoDB OK!";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
