<?php ob_start();
//
//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com
//
include("config.php");
include("class.session_handler.php");
include_once("send_email.php");
include_once("functions.php");

$msg="";
$to=1;
$lightbox = "";

mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD);
mysql_select_db(DB_NAME);

if(isset($_REQUEST['str'])) {
	$res=mysql_query("select * from ".USERS." where username ='".mysql_real_escape_string(base64_decode($_REQUEST['str']))."' and confirm_string='".mysql_real_escape_string($_REQUEST['cs'])."'");
	if(mysql_num_rows($res) == 0) {
		header("Location:login.php");
		exit;
	} else {
		$row=mysql_fetch_array($res);
		mysql_query("UPDATE ".USERS." SET confirm = 1 WHERE id = ".$row['id'].";");
        if (!empty($row['phone']) && !empty($row['country']) && !empty($row['provider']) && !empty($row['confirm_phone'])) {
            sl_send_phone_confirm_sms($row['id'], $row['phone'], $row['country'], $row['provider'], $row['confirm_phone']);
        }
		if (REQUIRELOGINAFTERCONFIRM) {
		    session::init(); // User must log in AFTER confirming (they're not allowed to before)
		} else {
		    initSessionData($row); //Optionally can login with confirm URL
		}
	 }
	
} else {
	header("Location:login.php");
	exit;
}

/*********************************** HTML layout begins here  *************************************/

include("head.html"); ?>

<!-- Add page-specific scripts and styles here, see head.html for global scripts and styles  -->
<script language="javascript" src="js/lightbox-hc.js"></script>

<!-- jquery file is for LiveValidation -->
<script type="text/javascript" src="js/jquery.livevalidation.js"></script>


<title>Worklist | Confirmation</title>

</head>

<body <?php echo $lightbox ?> >

<?php include("format.php"); ?>

<!-- ---------------------- BEGIN MAIN CONTENT HERE ---------------------- -->

<!-- Light Box Code Start -->
<div id="filter" onClick="closebox()"></div>
<div id="box" >
<p align="center">Email Confirmation</p>
<p><font  style="color:#624100; font-size:12px; font-family:Verdana;">Registration complete! Welcome to the Worklist. You can now start work.</font></p>
<p>&nbsp;</p>
<p align="center"><strong><a href="#" onClick="closebox()">Close</a></strong></p>
</div>
<!-- Light Box Code End -->
       	
    <h1>Email Confirmation</h1>
          
    <p>Registration complete! Welcome to the Worklist. You can now start working.</p>

<!-- ---------------------- end MAIN CONTENT HERE ---------------------- -->
                    
<?php include("footer.php"); ?>
