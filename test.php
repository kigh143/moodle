<?php
$forumid = 5;
$externalmoodleforum = '/sm/mod/forum/post.php?forum=' . $forumid;

if (isset($_SERVER['HTTP_REFERER'])) {
    die('We came back here.');
}
?>
<html>
    <body>
        <a href="<?php echo $externalmoodleforum; ?>">Go to Moodle</a>
    </body>
</html>