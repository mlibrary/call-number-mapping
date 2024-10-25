<?php

namespace Umich\CallNumberMapping;

class Database {

    private $database = '';
    private $username = '';
    private $password = '';
    private $host = 'db';
    private $link = null;

    function __construct() {
        $driver         = getenv('DB_DRIVER');
        $this->host     = getenv("{$driver}_HOST");
        $this->username = getenv("{$driver}_USER");
        $this->password = getenv("{$driver}_PASSWORD");
        $this->database = getenv("{$driver}_DATABASE");
        $this->link = new \PDO("mysql:host={$this->host};dbname={$this->database}", $this->username, $this->password);
    }

    public function insertSQL($sql, $placeholders = false) {
      if ($placeholders) {
        $this->getSQL($sql, $placeholders);
      }
      else {
        $this->getSQL($sql);
      }
      return $this->link->query("SELECT last_insert_id()")->fetch(\PDO::FETCH_NUM)[0];
    }

    public function getSQL($sql, $placeholders = false) {
      if (!$placeholders) {
        return $this->link->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
      }
      $sth = $this->link->prepare($sql);
      $sth->execute($placeholders);
      return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }
} 
