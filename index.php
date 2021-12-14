<?php
    function getUrl() {
        if(isset($_SERVER['PATH_INFO'])) {
            $url = rtrim($_SERVER['PATH_INFO'], '/'); // remove last slash
            $url = substr($url, 1); // remove first slash
            $url = filter_var($url, FILTER_SANITIZE_URL); // sanitize URL
            $url = explode('/', $url);
            array_shift($url);

            return $url;
        }
    }

    $jokes = [
        "My dog used to chase people on a bike a lot. It got so bad I had to take his bike away.",
        "How do you fix a damaged jack-o-lantern? You use a pumpkin patch.",
        "Where do young cows eat lunch? In the calf-ateria.",
        "Thanks for explaining the word 'many' to me. It means a lot.",
        "Today a man knocked on my door and asked for a small donation towards the local swimming pool. I gave him a glass of water.",
        "Why was the big cat disqualified from the race? Because it was a cheetah.",
        "I broke my finger at work today, on the other hand Im completely fine.",
        "What did the shy pebble wish for? That she was a little boulder."
    ];

    $notajokes = [
        'Test1',
        'Test2'
    ];

    $url = getUrl();

    header('Content-Type: application/json; charset=utf-8');
    if($url[0] == "") {
        echo '{"usage": {"/joke": "get a joke", "/notajoke": "get not a joke"}}';
    } else if($url[0] == 'joke') {
        echo '{"type": "joke", "message": "' . $jokes[array_rand($jokes)] . '"}';
    } else if($url[0] == 'notajoke') {
        echo '{"type": "notajoke", "message": "' . $notajokes[array_rand($notajokes)] . '"}';
    } else if($url[0] == 'rate') {
        if(isset($_POST['rating']) && isset($_POST['jokeId'])) {
            echo '{"type": "Your rating: ' . $_POST['rating'] . ' of joke '.$_POST['jokeId'].'"}';
        } else {
            echo '{"type": "error", "message": "wrong format"}';
        }
    } else {
        echo '{"type": "error", "message": "404"}';
    }
?>
