<?php
ini_set('display_errors',1);
error_reporting(E_ALL|E_STRICT|E_DEPRECATED);
set_include_path(
    realpath('./../lib')
    . PATH_SEPARATOR
    . get_include_path()
);
require_once 'tekhack/Broker.php';
require_once 'tekhack/Cache.php';

$eventId = null;
if (isset ($_GET['event'])) {
    $event = $_GET['event'];
    if (!ctype_digit($event)) {
        header('Status: 400 Bad Request');
        die(json_encode(array ('error' => array('message' => 'This event is invalid'))));
    }
    $eventId = $event;
}
$broker = new Broker();
$broker->setCache(new Cache());
$eventMembers = $broker->getEventParticipants($eventId);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Tek13 Hackathon</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href='http://fonts.googleapis.com/css?family=Righteous' rel='stylesheet' type='text/css'>
    <link href="/styles/style.css" rel="stylesheet" type="text/css" />
    <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
</head>

<body>

<div id="wrapper">
    <h1 id="title">php[tek] 2013 Hackathon</h1>

    <div id="header">
        <p><img id="hacking" src="/images/hack_laptop.png" width="250" height="" alt="hack"
                align="right"/>Welcome to the php[tek] 2013 hackathon. If you haven't registered on the
        <a href="https://www.facebook.com/events/364378906997047"
           title="Registration for the php[tek] 2013 hackathon on Facebook">event page</a>,
        you're missing out on winning an awesome prize.</p>
        <p>This hackathon is sponsosred by <a href="http://in2it.be" title="in2it vof">in2it
            vof</a>.</p>
    </div><!-- end header -->

    <h2>Current participants (<?php echo count($eventMembers['data']) ?>)</h2>

    <div id="winner">
        <div style="text-align: center;"><h2>And the winner is...</h2></div>
        <p><span id="message"></span>
        <img id="profilePic" src="" alt="" width="64" height="64" style="vertical-align: middle; margin-left: 25px;"/></p>
    </div>

    <div id="participants">
        <ul class="list">
            <?php foreach ($eventMembers['data'] as $member): ?>
            <li class="element">
                <a class="profileLink" href="http://facebook.com/<?php echo $member['username'] ?>"
                   title="<?php echo $member['name'] ?>"><img src="<?php echo $member['pic_crop']['uri'] ?>"
                    alt="<?php echo $member['name'] ?>" width="115" height="115"/></a>
            </li>
            <?php endforeach; ?>
        </ul>
        <div class="clear">&nbsp;</div>
    </div><!-- end participants -->
</div><!-- end wrapper -->

<script type="text/javascript">
    function hidePictures()
    {
        jQuery.each(jQuery('li.element').children(), function (index, value) {
            var looser = jQuery(value).children()[0];
            jQuery(looser).html('&nbsp;');
            jQuery(looser).hide();
        });
    }
    function randomizer()
    {
        var winner = Math.floor((Math.random() * <?php echo count($eventMembers['data']) ?>));
        jQuery.each(jQuery('li.element').children(), function (index, value) {
            if (index != winner) {
                var looser = jQuery(value).children()[0];
                jQuery(looser).html('&nbsp;');
                jQuery(looser).hide();
            } else {
                var lucky = jQuery(value).children()[0];
                jQuery(lucky).show();
                var string = jQuery(value).attr('title');
                jQuery('#message').text(string);
                jQuery('img#profilePic').attr('src', jQuery(lucky).attr('src'));
                jQuery('img#profilePic').attr('alt', jQuery(lucky).attr('alt'));
            }
        });
    }
    function pickWinner()
    {
        var count = 0;
        var int = self.setInterval(function() {
            randomizer();
            console.log('Running contestant #' + count);
            if (count == 5) {
                console.log(this);
                self.clearInterval(int);
                jQuery('#winner').show();
            }
            count++;
        }, 1500);
    }
    jQuery(document).ready(function () {
        jQuery('#winner').hide();
        jQuery('#hacking').click(function () {
            pickWinner();
        });
    });
</script>

</body>
</html>