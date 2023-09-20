<?php
require 'vendor/autoload.php'; // Make sure to require the autoload file from the library

use Ramsey\Uuid\Uuid;

function generateUUID() {
    return Uuid::uuid4()->toString();
}


function dd($d){
    echo "<pre>";

    var_dump($d);
exit;
}