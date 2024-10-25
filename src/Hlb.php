<?php
namespace Umich\CallNumberMapping;

class Hlb {
  private $templater;
  private $db;
  private $values;

  public function __construct($db, $templater = NULL) {
    $this->templater = $templater;
    $this->db = $db;
    $this->values = [
      'page' => [
        'authenticated' => false,
        'title' => '',
        'content' => [],
      ],
    ];
  }

  public function getSearchParams($narrow) {
    return [
      'name' => 'narrow',
      'options' => $this->getLeaves(),
      'selected' => $narrow,
    ];
  }

  public function addContent($template, $arguments = []) {
    $this->values['page']['content'][] = $this->templater->render($template . ".twig", $arguments);
    return $this;
  }

  public function pageTitle($str) {
    $this->values['page']['title'] = $str;
    return $this;
  }

  public function render() {
    return $this->templater->render("page.twig", $this->values['page']);
  }

  public function setAuthenticated($authenticated) {
    $this->values['page']['authenticated'] = $authenticated;
    return $this;
  }

  public function removeAssociation($child, $parent) {
    $sql = "DELETE FROM hlb3_topic_topic where child = :child and parent = :parent";
    $this->db->getSQL($sql, [":child" => $child, ":parent" => $parent]);
    return $this;
  }

  public function getTreeParams() {
    return ['tree' => $this->tree()];
  }

  private function tree() {
    $sql_start = "
SELECT id, name
FROM hlb3_topic
WHERE id NOT IN (SELECT child FROM hlb3_topic_topic)
ORDER BY trim(name)";

    $ret = $this->db->getSQL($sql_start);
    $str = '<ul>';
    foreach ($ret as $r) {
      $str .= '<li>';
      $str .= '<a href="index.php?narrow=' . htmlentities($r['id']) . '">' . htmlentities($r['name']) . '</a>';
      $str .= $this->subtree($r['id'], [$r['id']]);
      $str .= '</li>';
    }
    $str .= '</ul>';
    return $str;
  }

  private function subtree($id, $list) {
    $start = "
SELECT hlb3_topic.id, hlb3_topic.name
FROM hlb3_topic_topic
  LEFT JOIN hlb3_topic
    ON hlb3_topic.id=hlb3_topic_topic.child
WHERE hlb3_topic_topic.parent = :parent
ORDER BY trim(hlb3_topic.name)";
    $results = $this->db->getSQL($start, [":parent" => $id]);
    if (empty($results)) {
      return '';
    }
    
    $str = '<ul>';
    foreach ($results as $r) {
      $str .= '<li>' ;
      $str .=  '<a href="index.php?narrow=' . htmlentities($r['id']) . '">' . htmlentities($r['name']) . '</a>'; 
      $str .= '<form style="display: inline;" action="removeassociation.php" method="post"><input type="hidden" value="'.htmlentities($id).'" name="parent"><input type="hidden" value="'.htmlentities($r['id']).'" name="child"><input type="submit" value="Remove this association"></form>';
      if (array_search($r['id'],$list) === FALSE) {
        $str .= $this->subtree($r['id'], array_merge($list, array($r['id'])));
      } else {
        $str .= ' -- cycle detected';
      }
      $str .= '</li>';
    }
    $str .= '</ul>';
    return $str;
  }

  public function export($id) {
    $sql = "
SELECT 
  hlb3_topic.name,
  hlb3_lc.alphaStart,
  hlb3_lc.numStart,
  hlb3_lc.cutStart,
  hlb3_lc.alphaEnd,
  hlb3_lc.numEnd,
  hlb3_lc.cutEnd,
  hlb3_lc.notes
FROM
  hlb3_topic LEFT JOIN
  hlb3_lcMap ON hlb3_topic.id=hlb3_lcMap.topic LEFT JOIN
  hlb3_lc ON hlb3_lcMap.lc= hlb3_lc.id
WHERE
  :id = 0 OR hlb3_topic.id = :id
ORDER BY 
  hlb3_topic.name,
  hlb3_lc.alphaStart,
  hlb3_lc.numStart,
  hlb3_lc.cutStart,
  hlb3_lc.alphaEnd,
  hlb3_lc.numEnd,
  hlb3_lc.cutEnd,
  hlb3_lc.notes
";
    return $this->templater->render(
      'export.twig',
      ['topics' => $this->db->getSQL($sql, [":id" => $id])]
    );
  }

  public function getCategorySearchResults($search) {
    $search_sql = "
SELECT 
  id,
  name,
  c1+c2 AS count
FROM 
  (SELECT 
     hlb3_topic.id,
     hlb3_topic.name,
     count(hlb3_lcMap.lc) as c1,
     count(hlb3_deweyMap.dewey) as c2 
   FROM 
     hlb3_topic LEFT JOIN 
     hlb3_lcMap ON hlb3_topic.id=hlb3_lcMap.topic LEFT JOIN 
     hlb3_deweyMap ON hlb3_topic.id=hlb3_deweyMap.topic 
   GROUP BY 
     hlb3_topic.id) t1
WHERE
  name LIKE :search
ORDER BY 
  name
";
    $parents_sql = "
SELECT name
FROM hlb3_topic
JOIN hlb3_topic_topic
ON hlb3_topic.id = hlb3_topic_topic.parent
WHERE hlb3_topic_topic.child = :child
ORDER BY name";
    $children_sql = "
SELECT name
FROM hlb3_topic
JOIN hlb3_topic_topic
ON hlb3_topic.id = hlb3_topic_topic.child
WHERE hlb3_topic_topic.parent = :parent
ORDER by name
";

    $results = $this->db->getSQL($search_sql, [":search" => "%{$search}%"]);
    foreach ($results as &$result) {
      $result['parents'] = [];
      $result['children'] = [];
      foreach ($this->db->getSQL($parents_sql, [":child" => $result['id']]) as $parent) {
        $result['parents'][] = $parent['name'];
      }
      foreach ($this->db->getSQL($children_sql, [":parent" => $result['id']]) as $child) {
        $result['children'][] = $child['name'];
      }
    }
    return [
      'search' => $search,
      'results' => $results,
    ];
  }

  public function deleteTopic($id) {
    if (!isset($id) || empty($id)) {
      return $this;
    }

    $delete_queries = [
      "DELETE FROM hlb3_topic WHERE id = :id",
      "DELETE from hlb3_topic_topic WHERE parent = :id OR child = :id",
      "DELETE hlb3_lc FROM hlb3_lc JOIN hlb3_lcMap ON hlb3_lc.id = hlb3_lcMap.lc WHERE hlb3_lcMap.topic = :id",
      "DELETE hlb3_dewey FROM hlb3_dewey JOIN hlb3_deweyMap ON hlb3_dewey.id = hlb3_deweyMap.dewey WHERE hlb3_deweyMap.topic = :id",
      "DELETE FROM hlb3_lcMap WHERE topic = :id",
      "DELETE FROM hlb3_deweyMap WHERE topic = :id",
    ];
    foreach ($delete_queries as $sql) {
      $this->db->getSQL($sql, [":id" => $id]);
    }
    return $this;
  }

  private function getTopicsWithTopLevelNode() {
    $topics = [[ "id" => '0', "name" => 'Top Level Node']];
    foreach ($this->getTopics() as $topic) {
      $topics[] = $topic;
    }
    return $topics;
  }

  public function getAssociateParams() {
    return ['topics' => $this->getTopics()];
  }

  public function getReportFormParams() {
    return ['topics' => $this->getTopicsWithTopLevelNode()];
  }

  public function getCreateParams() {
    return ['topics' => $this->getTopicsWithTopLevelNode()];
  }

  public function createTopic($name) {
    return $this->db->insertSQL("INSERT INTO hlb3_topic SET name = :name", [":name" => $name]);
  }

  public function associateTopics($child, $parent) {
    //check for cycles:
    if ($parent == 0 || $child == 0) {
        return TRUE;
    }
    if ($parent == $child) {
      return FALSE;
    }
    $checked = array($child => TRUE);
    $to_check_children = [$child];
    while ($current = array_shift($to_check_children)) {
      foreach ($this->getChildrenIds($current) as $current_child) {
        if ($current_child == $parent) {
          return FALSE;
        }
        if (!$checked[$current_child]) {
          $checked[$current_child] = TRUE;
          $to_check_children[] = $current_child;
        }
      }
    }
    $this->db->insertSQL(
      "INSERT INTO hlb3_topic_topic SET parent = :parent, child = :child",
      [":parent" => $parent, ":child" => $child]
    );
    return TRUE;
  }

  public function getChildrenIds($parent) {
    $sql = "SELECT child FROM hlb3_topic_topic WHERE parent = :parent ORDER BY child";
    $ret = [];
    foreach ($this->db->getSQL($sql, [":parent" => $parent]) as $row) {
      $ret[] = $row['child'];
    }
    return $ret;
  }

  public function copy($type, $target, $mapping) {
    if($type == 'lc') {
      $sql = "
INSERT INTO hlb3_lc (alphaStart, numStart, cutStart, alphaEnd, numEnd, cutEnd, notes) 
SELECT alphaStart, numStart, cutStart, alphaEnd, numEnd, cutEnd, notes
FROM hlb3_lc
WHERE id = :id";
      $new_id = $this->db->insertSQL($sql, [":id" => $mapping]);
      $sql = "INSERT INTO hlb3_lcMap SET lc = :new_id, topic= :target";
      $this->db->getSQL($sql, [":new_id" => $new_id, ":target" => $target]);
    } elseif ($type == 'dewey') {
      $sql = "
INSERT INTO hlb3_dewey (numStart, numEnd, notes)
SELECT numStart, numEnd, notes
FROM hlb3_dewey
WHERE id = :id";
      $new_id = $this->db->insertSQL($sql, [":id" => $mapping]);
      $sql = "INSERT INTO hlb3_deweyMap SET dewey= :new_id, topic = :target";
      $this->db->getSQL($sql, [":new_id" => $new_id, ":target" => $target]);
    }
  }

  public function getLCAssignmentsByTopicId($id) {
    $sql = "
SELECT 
  id, 
  alphaStart, numStart, cutStart,
  alphaEnd, numEnd, cutEnd,
  notes 
FROM
  hlb3_lc
JOIN 
  hlb3_lcMap ON lc=id 
WHERE
  topic = :id
order by
  alphaStart, numStart, cutStart, alphaEnd, numEnd, cutEnd
";
    return $this->db->getSQL($sql, [":id" => $id]);
  }

  public function getDeweyAssignmentsByTopicId($id) {
    $sql = "
SELECT 
  id, numStart, numEnd, notes 
FROM
  hlb3_dewey
JOIN 
  hlb3_deweyMap on dewey=id 
WHERE
  topic = :id
ORDER BY
  numStart, numEnd
";
    return $this->db->getSQL($sql, [":id" => $id]);
  }

  public function getTopicNameById($id) {
    $data = $this->db->getSQL("SELECT name FROM hlb3_topic WHERE id = :id order by name", [":id" => $id]);
    return $data[0]['name'];
  }

  public function getMappingsParams($id) {
    return [
      'topic' => [
        'name' => $this->getTopicNameById($id),
        'id' => $id,
      ],
      'lc' => $this->getLCAssignmentsByTopicId($id),
      'dewey' => $this->getDeweyAssignmentsByTopicId($id),
      'select' => [
        'name' => 'target',
        'options' => $this->getLeaves(),
        'selected' => '',
      ],
    ];
  }

  public function getLeaves() {
    $sql = 'SELECT id, name FROM hlb3_topic WHERE id NOT IN (SELECT parent FROM hlb3_topic_topic) ORDER BY name';
    return $this->db->getSQL($sql);
  }

  public function getTopics() {
    return $this->db->getSQL("SELECT id, name FROM hlb3_topic ORDER BY name");
  }

  public function update($id, $alphaStart, $numStart, $cutStart, $alphaEnd, $numEnd, $cutEnd, $notes, $type) {
    if(!isset($numStart) || is_null($numStart) || strlen($numStart) == 0) {
      $numStart = 0;
    }
    if(!isset($numEnd) || is_null($numEnd) || strlen($numEnd) == 0) {
      $numEnd = 9999.999;
    }

    if ($type == 'lc') {
      $this->updateLC($id, $alphaStart, $numStart, $cutStart, $alphaEnd, $numEnd, $cutEnd, $notes);
    } elseif ($type == 'dewey') {
      $this->updateDewey($id, $numStart, $numEnd, $notes);
    }
  }

  private function updateLC($id, $alphaStart, $numStart, $cutStart, $alphaEnd, $numEnd, $cutEnd, $notes) {
    $sql = "
UPDATE
  hlb3_lc
SET 
  alphaStart = :alphaStart,
  numStart = :numStart,
  cutStart = :cutStart,
  alphaEnd = :alphaEnd,
  numEnd = :numEnd,
  cutEnd = :cutEnd,
  notes = :notes
WHERE
  id = :id";
    $params = [
      ":id" => $id, 
      ":alphaStart" => $alphaStart,
      ":numStart" => $numStart,
      ":cutStart" => $cutStart,
      ":alphaEnd" => $alphaEnd,
      ":numEnd" => $numEnd,
      ":cutEnd" => $cutEnd,
      ":notes" => $notes,
    ];
    $this->db->getSQL($sql, $params);
    return $this;
  }

  private function updateDewey($id, $numStart, $numEnd, $notes) {
    $sql = "
UPDATE
  hlb3_dewey
SET 
  numStart = :numStart,
  numEnd = :numEnd,
  notes = :notes
WHERE
  id = :id";
    $params = [
      ":id" => $id,
      ":numStart" => $numStart,
      ":numEnd" => $numEnd,
      ":notes" => $notes,
    ];
    $this->db->getSQL($sql, $params);
    return $this;
  }

  public function add($topicId, $alphaStart, $numStart, $cutStart, $alphaEnd, $numEnd, $cutEnd, $notes, $type) {
    if (!isset($numStart) || is_null($numStart) || strlen($numStart) == 0) {
      $numStart = 0;
    }
    if (!isset($numEnd) || is_null($numEnd) || strlen($numEnd) == 0) {
      $numEnd = 9999.999;
    }
    
    if($type == 'lc') {
      $this->addLc($topicId, $alphaStart, $numStart, $cutStart, $alphaEnd, $numEnd, $cutEnd, $notes);
    } elseif ($type == 'dewey') {
      $this->addDewey($topicId, $numStart, $numEnd, $notes);
    }
    return $this;
  }

  private function addLc($topicId, $alphaStart, $numStart, $cutStart, $alphaEnd, $numEnd, $cutEnd, $notes) {
    $sql = "
INSERT INTO
  hlb3_lc
SET 
  alphaStart = :alphaStart,
  numStart = :numStart,
  cutStart = :cutStart,
  alphaEnd = :alphaEnd,
  numEnd = :numEnd,
  cutEnd = :cutEnd,
  notes = :notes";
    $parameters = [
      ':alphaStart' => $alphaStart,
      ':numStart' => $numStart,
      ':cutStart' => $cutStart,
      ':alphaEnd' => $alphaEnd,
      ':numEnd' => $numEnd,
      ':cutEnd' => $cutEnd,
      ':notes' => $notes,
    ];
    $lcId = $this->db->insertSQL($sql, $parameters);

    $sql = 'INSERT INTO hlb3_lcMap SET lc = :lcId, topic= :topicId';
    $this->db->getSQL($sql, [ ':lcId' => $lcId, ':topicId' => $topicId ]);
    return $this;
  }

  private function addDewey($topicId, $numStart, $numEnd, $notes) {
    $sql = "
INSERT INTO
  hlb3_dewey
SET
  numStart = :numStart,
  numEnd = :numEnd,
  notes = :notes";
    $parameters = [':numStart' => $numStart, ':numEnd' => $numEnd, ':notes' => $notes];
    $deweyId = $this->db->insertSQL($sql, $parameters);
    
    $sql = 'INSERT INTO hlb3_deweyMap SET dewey = :deweyId, topic = :topicId';
    $this->db->getSQL($sql, [':deweyId' => $deweyId, ':topicId' => $topicId]);
    return $this;
  }

  public function delete($type, $id) {
    if ($type == 'lc') {
      $this->deleteLc($id);
    } elseif ($type == 'dewey') {
      $this->deleteDewey($id);
    }
    return $this;
  }

  private function deleteLc($lcId) {
    $this->db->getSQL("DELETE FROM hlb3_lc WHERE id  = :lcId", [':lcId' => $lcId]);
    $this->db->getSQL("DELETE FROM hlb3_lcMap WHERE lc = :lcId", [':lcId' => $lcId]);
    return $this;
  }

  private function deleteDewey($deweyId) {
    $this->db->getSQL("DELETE FROM hlb3_dewey WHERE id = :deweyId", [':deweyId' => $deweyId]);
    $this->db->getSQL("DELETE FROM hlb3_deweyMap WHERE dewey = :deweyId", [':deweyId' => $deweyId]);
    return $this;
  }

  public function getEditParams($type, $id) {
    if ($type == 'lc') {
      return $this->getLcEditParams($id);
    }
    elseif ($type == 'dewey') {
      return $this->getDeweyEditParams($id);
    }
    return [];
  }

  public function getLcEditParams($id) {
    $rangeSql = "
SELECT
  id,
  alphaStart, numStart, cutStart,
  alphaEnd, numEnd, cutEnd,
  notes
FROM hlb3_lc
WHERE id = :id
ORDER BY
   alphaStart, numStart, cutStart, alphaEnd, numEnd, cutEnd, notes";
    $topicSql = "
SELECT name, id 
FROM hlb3_topic
JOIN hlb3_lcMap ON hlb3_topic.id = hlb3_lcMap.topic
WHERE hlb3_lcMap.lc = :id
ORDER BY name
";
    return [
      'type' => 'lc',
      'range' => $this->db->getSQL($rangeSql, [':id' => $id])[0],
      'topic' => $this->db->getSQL($topicSql, [':id' => $id])[0],
    ];
  }

  public function getDeweyEditParams($id) {
    $rangeSql = "
SELECT numStart, numEnd, notes
FROM hlb3_dewey
WHERE id = :id
ORDER BY numStart, numEnd, notes";
    $topicSql = "
SELECT name, id
FROM hlb3_topic
JOIN hlb3_deweyMap ON hlb3_topic.id = hlb3_deweyMap.topic
WHERE hlb3_deweyMap.dewey = :id
ORDER BY name";
    return [
      'type' => 'dewey',
      'range' => $this->db->getSQL($rangeSql, [':id' => $id])[0],
      'topic' => $this->db->getSQL($topicSql, [':id' => $id])[0],
    ];
  }

  public function getDeweySearchResults($params) {
    $sql = "
SELECT 
  hlb3_topic.id,
  hlb3_topic.name,
  hlb3_dewey.numStart,
  hlb3_dewey.numEnd,
  hlb3_dewey.notes
FROM
  hlb3_topic INNER JOIN
  hlb3_deweyMap ON hlb3_topic.id=hlb3_deweyMap.topic INNER JOIN
  hlb3_dewey ON hlb3_deweyMap.dewey=hlb3_dewey.id
WHERE
  1=1
";
    if ($params['num_op'] == '<=') {
      $sql .= " AND ( hlb3_dewey.numStart <= :num )";
      $sql_params = [':num' => $params['num']];
    }
    elseif ($params['num_op'] == '>=') {
      $sql .= " AND ( hlb3_dewey.numEnd >= :num )";
      $sql_params = [':num' => $params['num']];
    }
    elseif ($params['num_op'] == '=') {
      $sql .= " AND ( :num BETWEEN hlb3_dewey.numStart AND hlb3_dewey.numEnd ) ";
      $sql_params = [':num' => $params['num']];
    }
    else {
      $sql_params = [];
    }
    return [
      'type' => 'dewey',
      'description' => "dewey {$params['num_op']} {$params['num']}",
      'results' => $this->db->getSQL($sql, $sql_params),
    ];
  }

  public function getLcSearchResults($params) {
    $sql = "
SELECT 
  hlb3_topic.id,
  hlb3_topic.name,
  hlb3_lc.alphaStart,
  hlb3_lc.numStart,
  hlb3_lc.cutStart,
  hlb3_lc.alphaEnd,
  hlb3_lc.numEnd,
  hlb3_lc.cutEnd,
  hlb3_lc.notes
FROM 
  hlb3_topic LEFT JOIN 
  hlb3_lcMap on hlb3_topic.id=hlb3_lcMap.topic LEFT JOIN
  hlb3_lc on hlb3_lcMap.lc=hlb3_lc.id
WHERE
  ( :alpha between hlb3_lc.alphaStart and hlb3_lc.alphaEnd )
";
    $sql_params = [ ":alpha" => $params['alpha'] ];
    if (isset($params['num']) and strlen($params['num'])) {
      $sql_params[':num'] = $params['num'];
      if ($params['num_op'] == '<=') {
        $sql .= " AND (hlb3_lc.alphaStart < :alpha OR hlb3_lc.numStart <= :num)";
      }
      elseif ($params['num_op'] == '>=') {
        $sql .= " AND (hlb3_lc.alphaEnd > :alpha OR hlb3_lc.numEnd >= :num) ";
      } elseif ($params['num_op'] == '=') {
        $sql .= " AND ((hlb3_lc.alphaStart < :alpha OR hlb3_lc.numStart <= :num ) and
                    (hlb3_lc.alphaEnd > :alpha OR hlb3_lc.numEnd >= :num ))";
      }
      if (isset($params['cut']) && strlen($params['cut'])) {
        $sql_params[':cut'] = $params['cut'];
        if ($params['cut_op'] == '<=') {
          $sql .= " AND (hlb3_lc.alphaStart < :alpha or hlb3_lc.numStart != :num or hlb3_lc.cutStart <= :cut) ";
        }
        elseif ($params['cut_op'] == '>=') {
           $sql .= " AND (hlb3_lc.alphaEnd > :alpha OR hlb3_lc.numEnd != :num OR hlb3_lc.cutEnd >= :cut)";
        }
        elseif ($params['cut_op'] == '=') {
          $sql .= " AND ((hlb3_lc.alphaStart < :alpha OR hlb3_lc.numStart <= :num) AND
                    (hlb3_lc.alphaEnd > :alpha OR hlb3_lc.numEnd >= :num)) ";
        }
      }
    }
    elseif (isset($params['cut']) && strlen($params['cut'])) {
      $sql_params[':cut'] = $params['cut'];
      if ($params['cut_op'] == '<=') {
        $sql .= " AND (hlb3_lc.alphaStart < :alpha OR hlb3_lc.cutStart <= :cut ) ";
      }
      elseif ($params['cut_op'] == '>=') {
        $sql .= " AND (hlb3_lc.alphaEnd > :alpha OR hlb3_lc.cutEnd >= :cut ) ";
      }
      elseif ($params['cut_op'] == '=') {
        $sql .= " AND ((hlb3_lc.cutStart IS NULL OR hlb3_lc.alphaStart < :alpha OR hlb3_lc.cutStart <= :cut) AND 
                       (hlb3_lc.cutEnd IS NULL OR hlb3_lc.alphaEnd > :alpha OR hlb3_lc.cutEnd >= :cut)) ";
      }
    }

    $sql .= ' ORDER BY hlb3_topic.name asc, hlb3_lc.alphaStart asc, hlb3_lc.numStart asc, hlb3_lc.cutStart asc';
    return [
      'type' => 'lc',
      'results' => $this->db->getSQL($sql, $sql_params),
      'description' => "alpha = {$params['alpha']}, num {$params['num_op']} {$params['num']}, cut {$params['cut_op']} {$params['cut']}",
    ];
  }
}
    
