<?php
$pgTitle = 'Three-Tiered High-Level Browse Categories';
require_once('/www/www.lib/web/phase2/php/header_top.php');
require_once('/www/www.lib/web/phase2/php/header_bottom.php');

if(isset($_REQUEST['l3'])) $l3 = $_REQUEST['l3'];
else $l3 = null;
if(isset($_REQUEST['l2'])) $l2 = $_REQUEST['l2'];
else $l2 = null;
if(isset($_REQUEST['l1'])) $l1 = $_REQUEST['l1'];
else $l1 = null;
  
function connectTopicmap() {
	$host = "mysql-web";
	$username = "topicmap";
	$password = "420plumb";
	$database = "topicmap";

	$link = mysql_pconnect($host, $username, $password) or die ("Could not connect to $host");
	mysql_select_db ($database,$link) or die ("Could not select Database: $database");
	return $link;
}

$pgTitle = 'Three-Tiered High-Level Browse Categories';
$inc_path = '/includes/dropdown/'

?>
<html>
<head>
<?php
	//include( $_SERVER['DOCUMENT_ROOT'] . $inc_path . "php/header_top.php" );
?>
</head>

<?php
	$pgTitle = 'University of Michigan Library Three-Tiered High-Level Browse Categories';
	//include( $_SERVER['DOCUMENT_ROOT'] . $inc_path . "php/header_bottom.php" );
?>
<!-- BEGIN PAGE CONTENT -->
<!--Comments and questions can be directed to the Electronic Access Unit: <a href="mailto:e-a-unit@umich.edu">e-a-unit@umich.edu</a><br /><br /> -->
<span style="font-size:x-small;">(View the entire mapping in <a href="./xml.php">XML</a>)</span>

<?php
$link = connectTopicmap();
// this gets it all:	
//	select lo.name,lt.name,lm.lc,lc.alphaStart,lc.numStart,lc.alphaEnd,lc.numEnd from lc, lcMap lm,levelOneTopic lo, levelTwoTopic lt,encompasses e where e.levelOne=lo.id and e.levelTwo=lt.id and lt.id=lm.levelTwo and lm.lc=lc.id;


echo "<br/><a href=\"./index.php\">&lt;&lt; Back</a>";
echo "<dl>";

//For each level one topic...
//$levelOneTopic_query = 'select id,name from levelOneTopic where hidden=0 order by name';
$levelOneTopic_query = 'select id,name from hlb3_topic where id not in (select child from hlb3_topic_topic) and hidden=0 order by trim(name)';
$levelOneTopic_result = mysql_query($levelOneTopic_query,$link) or
		die("Couldn't execute '$levelOneTopic_query'");
while( $topic_one_array = mysql_fetch_array($levelOneTopic_result,MYSQL_ASSOC) ) {

	//Get the id, and print the name
	$level_one_topic_id = $topic_one_array['id'];
  if($level_one_topic_id != $l1) continue;
	echo "<dt><b>{$topic_one_array['name']}</b></dt>\n";
	//echo "\t<dd>\n";

	//For each level two topic ...
	$levelTwoTopic_query = "
SELECT 
  l2.id,
  l2.name,
  count(p.id) parents 
FROM 
  hlb3_topic l2 JOIN 
  hlb3_topic_topic e ON e.child=l2.id LEFT JOIN
  hlb3_topic_topic p ON p.parent=l2.id 
WHERE
  e.parent=$level_one_topic_id
GROUP BY 
    l2.id
ORDER BY 
  trim(name) ";
	$levelTwoTopic_result = mysql_query($levelTwoTopic_query,$link) or die( "Couldn't execute '$levelTwoTopic_query'");
	while( $topic_two_array = mysql_fetch_array($levelTwoTopic_result, MYSQL_ASSOC) ) {
		$level_two_topic_id = $topic_two_array['id'];
    if($level_two_topic_id != $l2) continue;
    if($topic_two_array['parents'] == 0) {
  		//get the id and print the name
  		echo "\t<dd>{$topic_two_array['name']} ";
		
  		if($level_one_topic_id == $l1 && $level_two_topic_id == $l2 ) {
  			//For each call number associated with this level 2 topic ...
  			$call_num_query = "
SELECT DISTINCT
  hlb3_lc.alphaStart,
  hlb3_lc.numStart,
  hlb3_lc.cutStart,
  hlb3_lc.alphaEnd,
  hlb3_lc.numEnd,
  hlb3_lc.cutEnd 
FROM
  hlb3_lc,
  hlb3_lcMap lm 
WHERE
  lm.topic=$level_two_topic_id AND
  hlb3_lc.id=lm.lc 
ORDER BY
  hlb3_lc.alphaStart,
  hlb3_lc.numStart,
  hlb3_lc.cutStart
";
  			$call_num_result = mysql_query($call_num_query,$link) or die("Couldn't execute '$call_num_query'");
  			echo "<ul>";
  			while($call = mysql_fetch_array($call_num_result,MYSQL_ASSOC)) {
  
  				//print the call numbers:
  				if(isset( $call['numStart']) )
  					$call['numStart'] = sprintf("%d",$call['numStart']);
  				if(isset( $call['numEnd']) )
  					$call['numEnd'] = sprintf("%d",$call['numEnd']);
				
  				// so we can compare them easily, and we don't put ranges for things which really
  				// are not.
  				$start_string = "{$call['alphaStart']} {$call['numStart']} {$call['cutStart']}";
  				$end_string = "{$call['alphaEnd']} {$call['numEnd']} {$call['cutEnd']}";

  				if($start_string == $end_string) {
  					echo "\t\t<li>$start_string</li>\n";
  				}
  				else {
  					echo "\t\t<li> $start_string - $end_string </li>\n";
  				}
  			}
  			echo "</ul></dd>";
  		}
  		else {
  			echo " - [<a href=\"./call_number.php?l1=$level_one_topic_id&l2=$level_two_topic_id\">Call numbers</a>]</dd>\n";
  		}
  	} else {
  		echo "\t<dd><dl><dt style=\"margin-top: 0px;\">{$topic_two_array['name']}</dt>";
      $levelThreeTopic_query = "
SELECT 
  l2.id,
  l2.name,
  count(p.id) parents 
FROM 
  hlb3_topic l2 JOIN 
  hlb3_topic_topic e ON e.child=l2.id LEFT JOIN
  hlb3_topic_topic p ON p.parent=l2.id 
WHERE
  e.parent=$level_two_topic_id
GROUP BY 
    l2.id
ORDER BY 
  trim(name) ";
  	  $levelThreeTopic_result = mysql_query($levelThreeTopic_query,$link) or die( "Couldn't execute '$levelThreeTopic_query'");
  	  while( $topic_three_array = mysql_fetch_array($levelThreeTopic_result, MYSQL_ASSOC) ) {
        $level_three_topic_id = $topic_three_array['id'];
        if($level_three_topic_id != $l3) continue;
    	  echo "\t<dd>{$topic_three_array['name']} ";
    	  if($level_one_topic_id == $l1 && $level_two_topic_id == $l2 && $level_three_topic_id == $l3 ) {
          //For each call number associated with this level 2 topic ...
          $call_num_query = "
SELECT DISTINCT
  hlb3_lc.alphaStart,
  hlb3_lc.numStart,
  hlb3_lc.cutStart,
  hlb3_lc.alphaEnd,
  hlb3_lc.numEnd,
  hlb3_lc.cutEnd 
FROM
  hlb3_lc,
  hlb3_lcMap lm 
WHERE
  lm.topic=$level_three_topic_id AND
  hlb3_lc.id=lm.lc 
ORDER BY
  hlb3_lc.alphaStart,
  hlb3_lc.numStart,
  hlb3_lc.cutStart
";
          $call_num_result = mysql_query($call_num_query,$link) or die("Couldn't execute '$call_num_query'");
          echo "<ul>";
          while($call = mysql_fetch_array($call_num_result,MYSQL_ASSOC)) {

            //print the call numbers:
            if(isset( $call['numStart']) )
              $call['numStart'] = sprintf("%d",$call['numStart']);
            if(isset( $call['numEnd']) )
              $call['numEnd'] = sprintf("%d",$call['numEnd']);

            // so we can compare them easily, and we don't put ranges for things which really
            // are not.
            $start_string = "{$call['alphaStart']} {$call['numStart']} {$call['cutStart']}";
            $end_string = "{$call['alphaEnd']} {$call['numEnd']} {$call['cutEnd']}";

            if($start_string == $end_string) {
              echo "\t\t\t<li>$start_string</li>\n";
            }
            else {
              echo "\t\t\t<li> $start_string - $end_string </li>\n";
            }
          }
          echo "\t\t</ul>\n";
          echo "\t\t</dd>\n";
        } else {
  			  echo " - [<a href=\"./call_number.php?l1=$level_one_topic_id&l2=$level_two_topic_id&l3=$level_three_topic_id\">Call numbers</a>]</dd>\n";
        }
      }
      print "\t\t</dl>\n";
      print "\t</dd>\n";
    } 
  }
	echo "\t</dt>\n";
}	
echo "</dl>\n";
echo "<a href=\"./index.php\">&lt;&lt; Back</a>";
?>	
<!-- END PAGE CONTENT -->
<!-- No need to close off any tags - it's all taken care of -->
<?php
require_once('/www/www.lib/web/phase2/php/footer.php');
        //include( $_SERVER['DOCUMENT_ROOT'] . $inc_path . "php/footer.php" );
?>
