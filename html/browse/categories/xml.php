<?php

$pgTitle = 'Three-Tiered High-Level Browse Categories';

header("Content-Type: text/xml;charset=utf-8");
echo '<?xml version="1.0" encoding="utf-8"?>';

$link = connectTopicmap();

echo "<hlb title=\"$pgTitle\">\n";

//For each level one topic...
$level_one_topic_result = get_level_one_topics($link);
$level_two_topic_statement = get_next_level_topics($link);
$level_three_topic_statement = get_next_level_topics($link);
$call_num_statement = get_call_num_statement($link);

foreach ($level_one_topic_result as $topic_one_array) {

	//Get the id, and print the name
	$level_one_topic_id = $topic_one_array['id'];
	echo "\t<subject name=\"" . process_string($topic_one_array['name']) ."\">\n";

	//For each level two topic ...
  $level_two_topic_statement->execute([':parent_id' => $level_one_topic_id ]);
  foreach ($level_two_topic_statement as $topic_two_array) {

		//get the id and print the name
		$level_two_topic_id = $topic_two_array['id'];
		echo "\t\t<topic name=\"" . process_string($topic_two_array['name']) . "\">\n";

		if ($topic_two_array['parents'] > 0) {
      $level_three_topic_statement->execute([":parent_id" => $level_two_topic_id]);
      foreach ($level_three_topic_statement as $topic_three_array) {
		    echo "\t\t\t<sub-topic name=\"" . process_string($topic_three_array['name']) . "\">\n";
        $level_three_topic_id = $topic_three_array['id'];
        $call_num_statement->execute([":topic_id" => $level_three_topic_id]);
        foreach ($call_num_statement as $call) {
          echo "\t\t\t\t" . process_call_number($call) . "\n";
        }
		    echo "\t\t\t</sub-topic>\n";
      }
    }
    $call_num_statement->execute([":topic_id" => $level_two_topic_id]);
    foreach ($call_num_statement as $call) {
			echo "\t\t\t" . process_call_number($call) . "\n";
		}
		echo "\t\t</topic>\n";
	}
	echo "\t</subject>\n";
}	
echo "</hlb>\n";

function connectTopicmap() {
  $driver   = getenv('DB_DRIVER');
	$host     = getenv("{$driver}_HOST");
	$username = getenv("{$driver}_USER");
	$password = getenv("{$driver}_PASSWORD");
	$database = getenv("{$driver}_DATABASE");
  return new PDO("mysql:host={$host};dbname={$database}", $username, $password);
}

function get_level_one_topics($link) {
  return $link->query("
SELECT
  id,
  name
FROM
  hlb3_topic
WHERE
  id NOT IN (SELECT child FROM hlb3_topic_topic) AND
  hidden=0
ORDER BY
  TRIM(name)
");
}

function get_next_level_topics($link) {
 return $link->prepare("
SELECT 
  l2.id,
  l2.name,
  count(p.id) parents 
FROM 
  hlb3_topic l2 JOIN 
  hlb3_topic_topic e ON e.child=l2.id LEFT JOIN
  hlb3_topic_topic p ON p.parent=l2.id 
WHERE
  e.parent = :parent_id
GROUP BY 
    l2.id
ORDER BY 
  trim(name) ");
}
function get_call_num_statement($link) {
  return $link->prepare("
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
  lm.topic = :topic_id AND
  hlb3_lc.id=lm.lc
ORDER BY
  hlb3_lc.alphaStart,
  hlb3_lc.numStart,
  hlb3_lc.cutStart
");
}

function process_call_number($call) {
  if (isset($call['numStart'])) {
    if ($call['numStart'] - floor($call['numStart']) == 0) {
      $call['numStart'] = sprintf("%d", $call['numStart']);
    } else {
      $call['numStart'] = preg_replace('/0+$/','', $call['numStart']);
    }
  }
  if (isset($call['numEnd'])) {
    if ($call['numEnd'] - floor($call['numEnd']) == 0) {
      $call['numEnd'] = sprintf("%d",$call['numEnd']);
    } else {
      $call['numEnd'] = preg_replace('/0+$/','', $call['numEnd']);
    }
  }
  $start_string = trim("{$call['alphaStart']} {$call['numStart']} {$call['cutStart']}");
  $end_string = trim("{$call['alphaEnd']} {$call['numEnd']} {$call['cutEnd']}");

  return "<call-numbers start=\"$start_string\" end=\"$end_string\" />";
}

function process_string($str) {
  return trim(htmlspecialchars($str));
}
