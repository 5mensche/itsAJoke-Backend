<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $conn = new mysqli("10.123.45.70", "lamam", "Admin123!", "itsajoke");

    if ($conn->connect_error) {
        die("Database Connection failed: " . $conn->connect_error);
    }

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
        $response = ['usage' => ['/jokes' => ['/get' => ['Description' => 'get the joke from provided ID', 'POST' => 'id={id}'], '/random' => ['Description' => 'get a random joke'], '/rate' => ['Description' => 'rate a joke with provided ID and rating', 'POST' => 'id={id}&rating={rating}'], "/listTop5" => ['Description' => 'List top 5 jokes']]], 'status' => '200'];
        echo json_encode($response);
    }

    $url = getUrl();

    header('Content-Type: application/json; charset=utf-8');

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
                    if(isset($_POST['id'])) {
                        $stmt = $conn->prepare("SELECT id, joke FROM jokes WHERE id LIKE ? LIMIT 1");
                        $stmt->bind_param('i', filter_var($_POST['id'], FILTER_VALIDATE_INT));
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $respose = ['type' => 'joke', 'id' => $row['id'], 'joke' => $row['joke'], 'status' => '200'];
                            }
                        } else {
                            $respose = ['type' => 'error', 'message' => 'unknown error', 'status' => '530'];
                        }
                        echo json_encode($respose);
                    } else {
                        errorWrongFormat();
                    }
                } else if ($url[1] == 'random') {
                    // -----------------------------------------------
                    // Get a random joke
                    // -----------------------------------------------
                    $stmt = $conn->prepare("SELECT id, joke FROM jokes ORDER BY RAND() LIMIT 1");
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $respose = ['type' => 'joke', 'id' => $row['id'], 'joke' => $row['joke'], 'status' => '200'];
                        }
                    } else {
                        $respose = ['type' => 'error', 'message' => 'unknown error', 'status' => '530'];
                    }
                    echo json_encode($respose);
                } else if ($url[1] == 'rate') {
                    // -----------------------------------------------
                    // Rate a joke
                    // -----------------------------------------------
                    if (isset($_POST['id']) && isset($_POST['rating'])) {
                        echo '{"type": "your rating: ' . $_POST['rating'] . ' of post ' . $_POST['id'] . '", "status": "200"}';
                    } else {
                        errorWrongFormat();
                    }
                } else if($url[1] == 'listtop5') {
                    // -----------------------------------------------
                    // get the top 5 posts
                    // -----------------------------------------------
                    errorNotImplemented();
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
