<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function getUrl() {
    if (isset($_SERVER['PATH_INFO'])) {
        $url = rtrim($_SERVER['PATH_INFO'], '/'); // remove last slash
        $url = substr($url, 1); // remove first slash
        $url = filter_var($url, FILTER_SANITIZE_URL); // sanitize URL
        $url = explode('/', $url);
        array_shift($url);

        return $url;
    }
}

function errorWrongFormat(){echo '{"type": "error", "message": "Wrong format", "status": "406"}';}
function errorNotImplemented(){echo '{"type": "error", "message": "Not implemented yet", "status": "501"}';}
function errorNotFound() {echo '{"type": "error", "message": "Not Found", "status": "404"}';}

function usage() {
    $response = ['usage' => ['/jokes' => ['/get' => ['Description' => 'get the joke from provided ID', 'POST' => 'id={id}'], '/random' => ['Description' => 'get a random joke'], '/rate' => ['Description' => 'rate a joke with provided ID and rating', 'POST' => 'id={id}&rating={rating}']]], 'status' => '406'];
    echo json_encode($response);
}

// \/ TEMP START \/
$jokes = [1 => "My dog used to chase people on a bike a lot. It got so bad I had to take his bike away.", 2 => "How do you fix a damaged jack-o-lantern? You use a pumpkin patch.", 3 => "Where do young cows eat lunch? In the calf-ateria.", 4 => "Thanks for explaining the word 'many' to me. It means a lot.", 5 => "Today a man knocked on my door and asked for a small donation towards the local swimming pool. I gave him a glass of water.", 6 => "Why was the big cat disqualified from the race? Because it was a cheetah.", 7 => "I broke my finger at work today, on the other hand Im completely fine.", 8 => "What did the shy pebble wish for? That she was a little boulder."];
// /\ TEMP END /\

$url = getUrl();

header('Content-Type: application/json; charset=utf-8');

// JSON ENCODE!!
if (isset($url[0])) {
    // -----------------------------------------------
    // Length 0
    // -----------------------------------------------
    if ($url[0] == 'jokes') {
        if (isset($url[1])) {
            if ($url[1] == 'get') {
                // -----------------------------------------------
                // Get a joke with id
                // -----------------------------------------------
                errorNotImplemented();
            } else if ($url[1] == 'random') {
                // -----------------------------------------------
                // Get a random joke
                // -----------------------------------------------
                $id = array_rand($jokes);
                $respose = ['type' => 'joke', 'id' => $id, 'joke' => $jokes[$id], 'status' => '200'];
                echo json_encode($respose);
            } else if ($url[1] == 'rate') {
                // -----------------------------------------------
                // Rate a joke
                // -----------------------------------------------
                if (isset($_POST['id']) && isset($_POST['rating'])) {
                    echo '{"type": "your rating: ' . $_POST['rating'] . ' of post ' . $_POST['id'] . '", "status" => "200"}';
                } else {
                    errorWrongFormat();
                }
            } else {
                errorNotFound();
            }
        } else {
            errorNotFound();
        }
    } else {
        errorNotFound();
    }
} else {
    usage();
}
