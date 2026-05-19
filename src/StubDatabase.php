<?php
/**
 * StubDatabase — drop-in replacement for Database using SQLite :memory:.
 *
 * Loaded by build-siteimprove when SITE_IMPROVE_STUB_DB=1 so that admin
 * pages can render without a real MySQL connection.  Because the stub is
 * required *before* the autoloader loads Database.php, PHP uses this
 * definition and never instantiates the real one.
 *
 * Only SELECT queries are exercised by the rendering pages targeted by
 * siteimprove.  The write helpers (add, update, delete, copy, …) are not
 * called during a GET render, so INSERT/UPDATE/DELETE never hit the stub.
 */

namespace Umich\CallNumberMapping;

class Database {

    private $link;

    public function __construct() {
        $this->link = new \PDO('sqlite::memory:');
        $this->link->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->createSchema();
        $this->seedFixtures();
    }

    // -----------------------------------------------------------------------
    // Schema
    // -----------------------------------------------------------------------

    private function createSchema(): void {
        $this->link->exec("
            CREATE TABLE hlb3_topic (
                id   INTEGER PRIMARY KEY,
                name TEXT NOT NULL
            );
            CREATE TABLE hlb3_topic_topic (
                parent INTEGER NOT NULL,
                child  INTEGER NOT NULL
            );
            CREATE TABLE hlb3_lc (
                id         INTEGER PRIMARY KEY,
                alphaStart TEXT,
                numStart   REAL,
                cutStart   TEXT,
                alphaEnd   TEXT,
                numEnd     REAL,
                cutEnd     TEXT,
                notes      TEXT
            );
            CREATE TABLE hlb3_lcMap (
                lc    INTEGER NOT NULL,
                topic INTEGER NOT NULL
            );
            CREATE TABLE hlb3_dewey (
                id       INTEGER PRIMARY KEY,
                numStart REAL,
                numEnd   REAL,
                notes    TEXT
            );
            CREATE TABLE hlb3_deweyMap (
                dewey INTEGER NOT NULL,
                topic INTEGER NOT NULL
            );
        ");
    }

    // -----------------------------------------------------------------------
    // Fixture data
    //
    // Topic tree:
    //   Arts & Humanities (1)
    //     └─ Literature (3)
    //   Science & Technology (2)
    //     └─ Physics (4)
    //
    // LC mappings  → topics 1 and 3
    // Dewey mappings → topics 2 and 4
    // -----------------------------------------------------------------------

    private function seedFixtures(): void {
        $this->link->exec("
            INSERT INTO hlb3_topic (id, name) VALUES
                (1, 'Arts & Humanities'),
                (2, 'Science & Technology'),
                (3, 'Literature'),
                (4, 'Physics');

            INSERT INTO hlb3_topic_topic (parent, child) VALUES
                (1, 3),
                (2, 4);

            INSERT INTO hlb3_lc (id, alphaStart, numStart, cutStart,
                                      alphaEnd,   numEnd,   cutEnd,  notes) VALUES
                (1, 'A',  0,    '', 'BZ', 9999.999, '', 'General Humanities'),
                (2, 'P',  0,    '', 'PZ', 9999.999, '', 'Literature & Languages'),
                (3, 'QA', 0,    '', 'QA', 999.999,  '', 'Mathematics (stub)');

            INSERT INTO hlb3_lcMap (lc, topic) VALUES
                (1, 1),
                (2, 3),
                (3, 2);

            INSERT INTO hlb3_dewey (id, numStart, numEnd, notes) VALUES
                (1, 500, 599.999, 'Natural Sciences'),
                (2, 530, 539.999, 'Physics'),
                (3, 100, 199.999, 'Philosophy & Psychology');

            INSERT INTO hlb3_deweyMap (dewey, topic) VALUES
                (1, 2),
                (2, 4),
                (3, 1);
        ");
    }

    // -----------------------------------------------------------------------
    // Public interface (mirrors the real Database class)
    // -----------------------------------------------------------------------

    public function insertSQL($sql, $placeholders = false) {
        $this->getSQL($sql, $placeholders);
        return $this->link->lastInsertId();
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
