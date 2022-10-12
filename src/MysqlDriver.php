<?php

namespace Ibrahim\MysqliDatabaseWrapper;

class MysqlDriver {

    private $connection;
    private $columns;
    private $query;
    private $sql;

    public function __construct($server,$user,$password,$database_name,$port = 3306)
    {
        $this->connection = mysqli_connect($server,$user,$password,$database_name,$port);
    }

    /*
     *  Data Definition Language (DDL)
     *  Create, Drop, Alter, Rename
     *  CREATE (TABLE)  => CREATE TABLE `Table_Name` (column dataType constraint position);
     *  DROP (TABLE)    => DROP TABLE `Table_Name`;
     *  Alter (ADD, DROP{index's,record}, RENAME, MODIFY, CHANGE)
     *  ADD     => ALTER TABLE `Table_Name` ADD Item DataType Constraint [O] Position [O]
     *  DROP    => have 2 job
     *            1) drop columns
     *            ALTER TABLE `Table_Name` DROP Item
     *            2) drop index's
     * foreign key => ALTER TABLE `Table_Name` DROP index `Foreign_Name`
     * primary key => ALTER TABLE `Table_Name` DROP primary key
     *  RENAME  => ALTER TABLE `Table_Name` RENAME `NEW_NAME`
     *  CHANGE  => ALTER TABLE `Table_Name` CHANGE (OLD) (OLD|NEW) DataType Constraint [O] Position [O]
     *  MODIFY  => ALTER TABLE `Table_Name` MODIFY Item DataType Constraint [O] Position [O]
     *  RENAME (TABLE)  => RENAME TABLE `Old_NAME` TO `NEW_NAME`
     */

    public function createTable($table, $items) {
        if (!empty($items)) {
            $this->sql = "CREATE TABLE IF NOT EXISTS `$table` (";
            foreach ($items as $key => $val) {
                $this->sql .= $key . ' ' . $val . ', ';
            }
            $this->sql = rtrim($this->sql,', ');
            $this->sql.= ");";
            $this->execute();
        } else {
            echo 'Must Insert At Least One Item';
        }
    }
    public function dropTable(...$tables) {
        $name = '';
        foreach ($tables as $item) {
            $name .= "`$item`,";
        }
        $this->sql = "DROP TABLE IF EXISTS " . $name;
        $this->execute();
    }

    /**
     * Alter => (ADD, DROP{index's,record}, RENAME, MODIFY, CHANGE)
     */

    public function alter($table) {
        $this->sql = "ALTER TABLE `$table` ";
        return $this;
    }

    public function addColumn($column, $dataType, $constraint = null, $position = null) {
        $this->sql .= "ADD IF NOT EXISTS `$column` $dataType $constraint $position, ";
        return $this;
    }
    public function dropColumn(...$columns) {
        foreach ($columns as $val) {
            $this->sql .= "DROP IF EXISTS `$val`, ";
        }
        return $this;
    }
    public function dropIndex(...$indexs) {
        foreach ($indexs as $index) {
            $index = strtoupper($index);
            if ($index === 'PRIMARY') {
                $this->sql .= "DROP $index KEY, ";
            } else {
                $this->sql .= "DROP INDEX $index, ";
            }
        }
        return $this;
    }

    public function modify($column, $dataType, $constraint = null, $position = null) {
        $this->sql .= "MODIFY IF EXISTS `$column` $dataType $constraint $position, ";
        return $this;
    }
    public function change($column, $new, $dataType, $constraint = null, $position = null) {
        $this->sql .= "CHANGE IF EXISTS `$column` $new $dataType $constraint $position, ";
        return $this;
    }

    public function renameTable($New_Name) {
        $this->sql .= "RENAME $New_Name";
        return $this;
    }

    public function add_primary_key($column) {
        $this->sql .= "ADD PRIMARY KEY (`$column`)";
        return $this;
    }
    public function add_foreign_key($column, $table, $columnTable) {
        $this->sql .= "ADD FOREIGN KEY (`$column`) REFERENCES `$table`(`$columnTable`)";
        return $this;
    }

    /*
     *  Data Manipulation Language (DML)
     *  Select, Insert, Update, Delete
     *  SELECT      => *, columns
     *  WHERE       => in(,,,,), and, or, arithmetic operators(=, !, <>, >, <, <=, >=), not
     *  ORDER BY    => order by col1,col2,... (DESC|ASC)
     *  LIMIT       => limit 'number'
     *  INSERT INTO => there have 3 ways
     *              1) insert into `table` values(,,,,)
     *              2) insert into `table`(,,,) values (,,,,)
     *              3) insert into `table` set `key` = 'value', `key` = 'value'
     *  UPDATE      => to update record
     *                -- update `table` set `key` = 'value', `key` = 'value' where ******
     *                 to update all record
     *                -- update `table` set `key` = 'value', `key` = 'value'
     *  DELETE      => delete all records
     *                -- delete from `table`
     *                 delete only one record
     *                -- delete from `table` where ******
     */

    public function select() {
        $this->sql = "SELECT ";
        return $this;
    }

    public function columns($item) {
        if (gettype($item) === 'string') {
            $this->columns .= ",$item ";
        } else {
            $order = '';
            foreach ($item as $val) {
                $order .= ",`$val`";
            }
            $this->columns .= $order;
        }
        return $this;
    }

    public function join($table) {
        $this->sql .= " INNER JOIN `$table`";
        return $this;
    }

    public function rightJoin($table) {
        $this->sql .= " RIGHT JOIN `$table`";
        return $this;
    }

    public function leftJoin($table) {
        $this->sql .= " LEFT JOIN `$table`";
        return $this;
    }

    public function on($rightTable, $key, $leftTable, $value) {
        $this->sql .= " ON `$rightTable`.`$key` = `$leftTable`.`$value`";
        return $this;
    }

    public function table($table) {
        $this->columns = ltrim($this->columns,',');
        $this->sql .= $this->columns . " FROM `$table`";
        return $this;
    }

    public function feature($keyWord,$item) {
        // keyWord = SUM Or MIN Or MAX Or COUNT
        $keyWord = strtoupper($keyWord);
        $this->columns .= ", $keyWord($item) ";
        return $this;
    }

    private function checkValue($value) {
        return (gettype($value) === 'integer') ? $value : "'$value'";
    }

    public function where() {
        $this->sql .= " WHERE ";
        return $this;
    }

    public function and() {
        $this->sql .= " AND ";
        return $this;
    }

    public function or() {
        $this->sql .= " OR ";
        return $this;
    }

    // operations {=,!=,<>,>,<,>=,<=}
    public function operations($key, $compare, $value) {
        $value = $this->checkValue($value);
        $this->sql .= "`$key` $compare $value ";
        return $this;
    }

    public function in($key, ...$values) {
        $this->sql .= " `$key` IN( ";
        foreach ($values as $val) {
            $val = $this->checkValue($val);
            $this->sql .= "$val, ";
        }
        $this->sql = rtrim($this->sql,', ');
        $this->sql .= ");";
        return $this;
    }

    public function orderBy($type, ...$item) {
        $order = '';
        foreach ($item as $val) {
            $order .= ",`$val`";
        }
        $order = ltrim($order,',');
        $this->sql .= " ORDER BY $order $type";
        return $this;
    }

    public function execute() {
        $this->sql = rtrim($this->sql,', ');
        $this->query = mysqli_query($this->connection,$this->sql);
        return $this;
    }

    public function limit($number) {
        $this->sql .= " LIMIT $number";
        return $this;
    }

    public function groupBy(...$items) {
        $this->sql .= "GROUP BY ";
        foreach ($items as $item) {
            $this->sql .= "`$item`, ";
        }
        $this->sql = rtrim($this->sql,', ');
        return $this;
    }

    public function fetch() {
        $this->execute();
        $this->columns = '';
        $this->sql = '';
        return mysqli_fetch_assoc($this->query);
    }

    public function fetchAll() {
        $this->execute();
        $data = [];
        while ($row = mysqli_fetch_assoc($this->query)) {
            $data[] = $row;
        }
        $this->columns = '';
        $this->sql = '';
        return $data;
    }

    public function insUp($keyWord, $table, $data) {
        // keyWord = insert into Or update
        $keyWord = strtoupper($keyWord);
        $data = $this->prepareData($data);
        $this->sql = "$keyWord `$table` SET $data";
        return $this;
    }

    private function prepareData ($data) {
        $finalData = '';
        foreach ($data as $key => $val) {
            $value = $this->checkValue($val);
            $finalData .= " `$key` = $value ,";
        }
        return rtrim($finalData,',');
    }

    public function delete($table) {
        $this->sql = "DELETE FROM `$table`";
        return $this;
    }

    public function __destruct() {
        mysqli_close($this->connection);
    }

}