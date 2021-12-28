<?php
    $conn = new mysqli("10.123.45.70", "<user>", "<pw>", "itsajoke");

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

    function errorWrongFormat($detail = 'null'){echo '{"type": "error", "message": "Wrong format", "detail": "' . $detail . '", "status": "406"}';}
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
                        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
                        $stmt = $conn->prepare("SELECT * FROM jokes WHERE id LIKE ? LIMIT 1");
                        $stmt->bind_param('i', $id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $response = ['type' => 'joke', 'id' => $row['id'], 'joke' => $row['joke'], 'rating' => $row['rating'], 'likes' => $row['likes'], 'dislikes' => $row['dislikes'], 'status' => '200'];
                            }
                        } else {
                            $response = ['type' => 'error', 'message' => 'unknown error', 'status' => '530'];
                        }
                        echo json_encode($response);
                    } else {
                        errorWrongFormat();
                    }
                } else if ($url[1] == 'random') {
                    // -----------------------------------------------
                    // Get a random joke
                    // -----------------------------------------------
                    $stmt = $conn->prepare("SELECT * FROM jokes ORDER BY RAND() LIMIT 1");
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $response = ['type' => 'joke', 'id' => $row['id'], 'joke' => $row['joke'], 'rating' => $row['rating'], 'likes' => $row['likes'], 'dislikes' => $row['dislikes'], 'status' => '200'];
                        }
                    } else {
                        $response = ['type' => 'error', 'message' => 'unknown error', 'status' => '530'];
                    }
                    echo json_encode($response);
                } else if ($url[1] == 'rate') {
                    // -----------------------------------------------
                    // Rate a joke
                    // -----------------------------------------------
                    if (isset($_POST['id']) && isset($_POST['rating'])) {
                        $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
                        $rating = filter_var($_POST['rating'], FILTER_VALIDATE_INT);

                        $stmt = $conn->prepare("SELECT rating, likes, dislikes FROM jokes WHERE id LIKE ? LIMIT 1;");
                        $stmt->bind_param('i', $id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $jokeRating = $row['rating'];
                                $jokeLikes = $row['likes'];
                                $jokeDislikes = $row['dislikes'];
                            }
                        } else {
                            errorWrongFormat('Unknown id');
                            exit();
                        }

                        if($rating == 1) {
                            $jokeLikes++;
                            $jokeRating++;
                        } else if($rating == 2) {
                            $jokeDislikes++;
                            $jokeRating--;
                        } else {
                            errorWrongFormat('Unknown rating type');
                            exit();
                        }

                        $stmt = $conn->prepare("UPDATE jokes SET rating = ?, likes = ?, dislikes = ? WHERE id LIKE ?;");
                        $stmt->bind_param('iiii', $jokeRating, $jokeLikes, $jokeDislikes, $id);
                        $stmt->execute();

                        $response = ['status' => '200'];

                        echo json_encode($response);
                    } else {
                        errorWrongFormat();
                    }
                } else if($url[1] == 'listtop5') {
                    // -----------------------------------------------
                    // get the top 5 posts
                    // -----------------------------------------------
                    $jokes = [];

                    $stmt = $conn->prepare("SELECT * FROM jokes ORDER BY rating DESC LIMIT 5;");
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $jokes['jokes'][] = ['type' => 'joke', 'id' => $row['id'], 'joke' => $row['joke'], 'rating' => $row['rating'], 'likes' => $row['likes'], 'dislikes' => $row['dislikes']];
                        }
                    }
                    $jokes['status'] '200';

                    echo json_encode($jokes);
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
