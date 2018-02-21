<?php
include("functions.php");
include("accesscontrol.php");

if (!empty($_GET['xml'])) {
  echo "<?xml version=\"1.0\" encoding=\"".$_SESSION['charset']."\" ?>\n<songlist>\n";
} else {
  echo "<html><head>";
  echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=".$_SESSION['charset']."\">\n";
  echo "<style type=\"text/css\">p {margin-bottom: 0; margin-top: 0;}</style>";
  echo "</head><body>";
}

$sid_array = explode(",",$sid_list);
$num_sids = count($sid_array);
for ($sid_index=0; $sid_index<$num_sids; $sid_index++) {
  $sql = "SELECT * FROM song WHERE SongID=$sid_array[$sid_index]";
  if (!$result = mysqli_query($db,$sql)) {
    echo("<b>SQL Error ".mysqli_errno($db).": ".mysqli_error($db)."</b><br>($sql)");
    exit;
  }
  $row = mysqli_fetch_object($result);
  if (!empty($_GET['xml'])) {
    echo "<song>\n";
  }
  for ($i=1; $i<7; $i++) {
    if (${"field".$i} != "") {
      if (!empty($_GET['xml'])) {
        $text = str_replace("&","&amp;",$row->{${"field".$i}});
        $text = str_replace("'","&apos;",$text);
        $text = preg_replace("#\[[^\[]*\]#u","",$text);  //to remove the chords and romaji markers
        echo "<".${"field".$i}.">".$text."</".${"field".$i}.">\n";
      } else {
        echo ${"layout".$i};
        if (${"newline".$i} == "YES")  echo "<br>\n";
        $text = str_replace("  "," &nbsp;",$row->{${"field".$i}});
        $text = preg_replace("#\r\n|\n|\r#u","<br>\n",$text);
        $text = preg_replace("#\[[^\[]*\]#u","",$text);  //to remove the chords and romaji markers
        echo $text;
        if (substr(${"layout".$i},-1) == "(")  echo ")";
      }
    }
  }
  if (!empty($_GET['xml'])) {
    echo "</song>\n";
  }
}
if (!empty($_GET['xml'])) {
  echo "</songlist>\n";
} else {
  echo "</body></html>";
}
?>
