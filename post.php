<script src="js/post.js"></script>
<script src="js/comment.js"></script>
<?php
require_once 'core.php';

include_once 'db.php';

if (!isset($_GET['aj'])) {
    include_once 'header.php';
}

// Handle invalid invoking

if (!isset($_GET['mode'])) {
    echo '<center><h3>Post not found.</h3></center>';
} else {
    if ($_GET['mode'] == 'v') {

        // view post with id = $_GET['id'] and make sure to display number of likes and an expandable comments section

        if (isset($_GET['post'])) {
            $the_post = $_GET['post'];
        } elseif (isset($_GET['id']) && isset($_GET['puid'])) {

            // Fetch post data

            $the_post = fetch_post($_GET['id'], $_GET['puid'], $pdo);
        } else {
            echo '<center><h3>Post not found.</h3></center>';
            die();
        }

        $puid = $the_post['puid'];
        $pid = $the_post['pid'];
        $poster_data = fetch_name($the_post['puid'], $pdo);
        $poster_name = $poster_data['fname'].' '.$poster_data['lname'];
        $nickname = $poster_data['nickname'];
        $caption = $the_post['caption'];
        process($caption);
        $time = $the_post['ptime'];
        $fetched_likes = fetch_likes($pid, $puid, $pdo);
        $conid = $puid.'_'.$pid;

        // Invalid post

        echo "<container id={$conid}>";
        echo "<div id='post'>";

        // Display poster name and post time

        $link = './profile?uid='.$puid;
        if (!file_exists('content/users/'.$puid.'/profile_picture.png')) {
            $profile_picture = "content/static/default_picture/{$poster_data['gender']}.jpg";
        } else {
            $profile_picture = 'content/users/'.$puid.'/profile_picture.png';
        }

        echo '<div class="posthead"><img class="post_thumb" src="'.$profile_picture.'"/>'.$poster_name."<div class='nickname'>(<a href={$link}>".$nickname.'</a>)</div></div><div id="postdate">Posted on <a href="post?mode=v&puid='.$puid.'&id='.$pid.'">'.date('l, F jS, Y', strtotime($time)).' at '.date('h:i A', strtotime($time)).'</a></div><div class="postcontent">'.$caption.'</div>';

        // Check for post likes

        echo '<div class="likes">';
        $like_icon = '<img id="like_icon" src="content/static/ui/like.png" height="20px" width="20px"/>';
        if ($fetched_likes) {
            $likes = $fetched_likes->fetchAll(PDO::FETCH_ASSOC);
            $uid = get_user() ['uid'];
            $liked = false;
            foreach ($likes as $like) {
                if ($like['uid'] == $uid) {
                    $liked = true;
                    break;
                }
            }

            // One like

            if ($fetched_likes->rowCount() == 1) {
                $liker = fetch_name($likes[0]['uid'], $pdo);
                echo $liker['fname'].' '.$liker['lname'].' likes this.';
            }

            // Two likes

            elseif ($fetched_likes->rowCount() == 2) {
                $first_liker = fetch_name($likes[0]['uid'], $pdo);
                $second_liker = fetch_name($likes[1]['uid'], $pdo);
                echo $like_icon.$first_liker['fname'].' '.$first_liker['lname'].' and '.$second_liker['fname'].' '.$second_liker['lname'].' like this.';
            }

            // More than two likes

            else {
                $liker = fetch_name($likes[0]['uid'], $pdo);
                echo $like_icon.$liker['fname'].' '.$liker['lname'].' and '.($fetched_likes->rowCount() - 1).' others like this.';
            }
        }

        // No likes

        else {
            echo $like_icon.'No likes yet';
            $liked = false;
        }

        if (!$liked) {
            echo "<button class='likebtn' onclick='like_post({$puid},{$pid})'>Like</button>";
        }

        if ($puid == get_user() ['uid']) {
            echo "<button class='delbtn' onclick='delete_post({$puid},{$pid})'>Delete Post</button>";
        }

        echo '</div>';
        $_GET['puid'] = $the_post['puid'];
        $_GET['pid'] = $the_post['pid'];
        include 'comment.php';

        $_GET['mode'] = 's';
        include 'comment.php';

        echo '</div>';
        echo '</container>';
    } elseif ($_GET['mode'] == 's') {

        // display the post form, the posting operation should be handled in ajax
        // Text area

        echo '<div class="postform"><table><textarea id="caption" rows="10" cols="85" placeholder="What\'s on your mind?"></textarea>';

        //To be implemented
        //echo '<input id="attachment" name="attachment" type="file" title="Attach"/>';

        // Submit button

        echo "<button id='submitPost' onclick='post()'>Post</button>";

        // Form end
        // Privacy menu

        echo '<select name="privacy" id="privacy" style="width:100px;margin-right:10px;"><option value="0">Public</option><option value="1">Private</option></select>';
        echo '</table></div>';
    }
}
