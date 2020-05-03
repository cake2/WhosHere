<HTML>
  <HEAD>
    <TITLE>Who's Here?!</TITLE>
    <META http-equiv="refresh" content="120">

<style type="text/css">
table.gridtable {
        font-family: verdana,arial,sans-serif;
        font-size:10px;
        color:#333333;
        border-width: 1px;
        border-color: #666666;
        border-collapse: collapse;
}
table.gridtable th {
        border-width: 1px;
        padding: 8px;
        border-style: solid;
        border-color: #666666;
        background-color: #dedede;
}
table.gridtable td {
        border-width: 1px;
        padding: 8px;
        border-style: solid;
        border-color: #666666;
        background-color: #ffffff;
}
</style>

  </HEAD>
<BODY>
<CENTER>
<img src="whoshere-logo.png"><br>
<b><i>You Can Modify Names and Enable Notifications for Assets Below:</i></b>
<p align="center">
<a href="configpage.php" TARGET="_BLANK">Options</a>
</p>

<?php
$output = shell_exec('systemctl status whoshere.service |grep "active (running)" | wc -l');
if($output == 0)
{
echo "<font color=red><b>The Collector Service is NOT Running!</b></font><br>(run \"<b>sudo systemctl start whoshere.service</b>\" or \"<b>python run.py</b>\" if that fails)<br><br>";
}
else
{echo "(<font color=green>The Collector Service is Running</font>)<br><br>";}
?>
<TABLE class="gridtable">
<TR><TH>Name</TH><TH>Times Seen</TH><TH>First Seen</TH><TH>Last Seen</TH><TH>Strength</TH><TH colspan="2">Notify Treshold</TH><TH>Update</TH></TR>

<?php

if(isset($_POST["asset"])){$asset = $_POST["asset"];}else{$asset = "";}
if(isset($_POST["Nickname"])){$Nickname = $_POST["Nickname"];}else{$Nickname = "";}
if(isset($_POST["DBTreshold"])){$DBTreshold = $_POST["DBTreshold"];}else{$DBTreshold = "";}
if(isset($_POST["Notify"])){$Notify = 1;}else{$Notify = 0;}
if(isset($_POST["ShowMore"])){$ShowMore = $_POST["ShowMore"];}else{$ShowMore = "No";}

$ShowMore=htmlspecialchars($ShowMore);

require_once 'dbconfig.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$Nickname = mysqli_real_escape_string($conn, $Nickname);
$DBTreshold = mysqli_real_escape_string($conn, $DBTreshold);
$Notify = mysqli_real_escape_string($conn, $Notify);
$asset = mysqli_real_escape_string($conn, $asset);

if(isset($asset)){
$sql = "CALL UpdateAssets('$Nickname','$Notify','$asset','$DBTreshold');";

$result = $conn->query($sql);
}

if($ShowMore == "Yes"){
$sql = "CALL ViewAllAssets();";
} else {
$sql = "CALL QuickViewAssets();";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {

//var_dump($row);

$Nickname = $row["Nickname"];
$DBTreshold = $row["DBTreshold"];
$Notify = $row["Notify"];
$MAC = $row["MAC"];
$TimesSeen = $row["TimesSeen"];
$FirstSeen = $row["FirstSeen"];
$LastSeen = $row["LastSeen"];
$SignalStrength = $row["SignalStrength"];
$SSIDs = $row["SSIDs"];
if($FirstSeen == "0000-00-00 00:00:00"){$FirstSeen = "";}

$FirstSeen = date('m/d/Y h:i:s A', strtotime($FirstSeen));
$LastSeen = date('m/d/Y h:i:s A', strtotime($LastSeen));

$Nickname=htmlspecialchars($Nickname);
$DBTreshold=htmlspecialchars($DBTreshold);
$Notify=htmlspecialchars($Notify);
$MAC=htmlspecialchars($MAC);
$TimesSeen=htmlspecialchars($TimesSeen);
$FirstSeen=htmlspecialchars($FirstSeen);
$LastSeen=htmlspecialchars($LastSeen);

?>
<TR><FORM METHOD="POST" ACTION="<?php echo $_SERVER['PHP_SELF']; ?>"><INPUT TYPE="hidden" NAME="asset" VALUE="<?php echo $MAC; ?>"><TD><INPUT TYPE="TEXT" NAME="Nickname" VALUE="<?php echo $Nickname; ?>"></TD>

<TD ALIGN="CENTER"><?php 
    echo $SignalStrength; 
  if ($SignalStrength > -81 && $SignalStrength < -66) {
    echo "<div style='background-color:red;color:white;font-weight:bold;'>NEAR</div>";
} elseif ($SignalStrength > -65) {
    echo "<div style='background-color:black;color:red;font-weight:bold;'>RED HOT</div>";
} else {
    echo "<div style='background-color:grey;color:white;font-weight:bold;'>FAR</div>";
} ?></TD>

<TD ALIGN="CENTER"><INPUT TYPE="CHECKBOX" NAME="Notify" <?php if($Notify == 1){echo "checked";}else{echo "unchecked";}?>></TD><TD><INPUT TYPE="TEXT" NAME="DBTreshold" VALUE="<?php echo $DBTreshold; ?>" SIZE="3"></TD><TD ALIGN="CENTER"><INPUT TYPE="SUBMIT" VALUE="Save"></TD></FORM></TR>

<?php
    }
}

$conn->close();
?>

</TABLE>
<br><br>
<?php if($ShowMore == "No"){?><FORM METHOD="POST" ACTION="<?php echo $_SERVER['PHP_SELF']; ?>"><INPUT TYPE="HIDDEN" NAME="ShowMore" VALUE="Yes"><INPUT TYPE="SUBMIT" VALUE="Show All"></FORM><?php } ?>
</CENTER>
</BODY>
</HTML>
