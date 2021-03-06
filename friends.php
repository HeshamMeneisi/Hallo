<?php

if (!isset($_GET['aj'])):
    echo '<title>Friends - Hallo</title>';
?>
 <script src="js/friends.js"></script>
 <container id='friends'>
<?php
endif;
include_once 'db.php';

include_once 'core.php';

include_once 'header.php';

if (is_logged()) {
    echo '<h1 class="page_title">Friends</h1>';
    $list_friends = isset($_GET['l']);
    $_GET['f'] = null;
    if (isset($_GET['r'])) {
        $uid = get_user() ['uid'];

        // display all friend requests

        $requests = fetch_sent_requests($pdo);
        echo '<div class="friends_container"><h2 id="pending_requests">Sent requests</h2>';
        if ($requests) {
            foreach ($requests as $request) {
                echo "<button id='cancelReq' style='margin-left:25px;' onclick='cancel_freq({$request['uid']})'>Cancel</button>";
                $_GET['person'] = $request;
                include 'person.php';
            }
        } else {
            echo '<center><h3>You have no sent requests</h3></center>';
        }

        echo '</div>';
        $requests = fetch_friend_requests($pdo);
        echo '<div class="friends_container"><h2 id="pending_requests">Friend requests</h2>';
        if ($requests) {
            foreach ($requests as $request) {
                echo "<button id='accReq' onclick='accept_freq({$request['uid']})'>Accept</button>";
                echo "<button id='rejReq' onclick='reject_freq({$request['uid']})'>Reject</button>";
                $_GET['person'] = $request;
                include 'person.php';
            }
        } else {
            echo '<center><h3>You have no friend requests</h3></center>';
        }

        echo '</div>';
    } else {
        echo 'Error retrieving requests.';
    }
}

echo '<div class="friends_container"><h2 id="pending_requests">Friends</h2>';

if ($list_friends) {
    $_GET['f'] = 1;
    if (isset($_GET['uid'])) {
        $uid = $_GET['uid'];
    } elseif (is_logged()) {
        $uid = get_user() ['uid'];
    } else {
        echo 'Invalid request.';
        die();
    }

    // display friend list of $uid

    $friends = fetch_friends($uid, $pdo);
    if ($friends) {
        foreach ($friends as $friend) {
            $_GET['person'] = $friend;
            include 'person.php';
        }
    } else {
        echo '<center><h3>You have no friends on your list, yet.</h3></center>';
    }
}

echo '</div>';

if (!isset($_GET['aj'])) {
    echo '</container>';
}
