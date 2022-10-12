# 🍗 Mysqli Database Helper 🍗

Mysqli Database Helper is a small php wrapper for mysql databases.

## installation

install once with composer:

```
composer require ibrahim/mysqli-database-wrapper
```

then add this to your project:

```php
require __DIR__ . '/vendor/autoload.php';
use Ibrahim\MysqliDatabaseWrapper\MysqlDriver;
$mysqli = new MysqlDriver();
```

## usage

```php
/* Connect To Database */
$mysqli = new MysqlDriver('server','user','password','database_name');

/* Create Table */
$mysqli->createTable('table_name',[
    'column' => 'data_type constraint position',
    'column' => 'data_type constraint position'
]);

/* Drop Table */
// Drop Only One Table
$mysqli->dropTable('table_name');
// Drop More Than One Table
$mysqli->dropTable('table_name','table_name','table_name','...');

/* Alter Table Use Add */
// column_name, data_type Is Required 
// constraint, position Is Optional
// Add Only One Column
$mysqli
    ->alter('table_name')
    ->addColumn('column_name','data_type','constraint','position')->execute();
// Add More Than One Column
$mysqli
    ->alter('table_name')
    ->addColumn('column_name','data_type','constraint','position')
    ->addColumn('column_name','data_type','constraint','position')->execute();
    
/* Alter Table Use DROP */
// -- DROP COLUMNS --
// DROP Only One Column
$mysqli
    ->alter('table_name')
    ->dropColumn('column_name')
    ->execute();
// DROP More Than One Column
$mysqli
    ->alter('table_name')
    ->dropColumn('column_name','column_name','column_name','...')
    ->execute();

// -- DROP Index's --
// DROP Only One Index
$mysqli->alter('table_name')
    ->dropIndex('column_name')
    ->execute();
// DROP Only One Index
$mysqli->alter('table_name')
    ->dropIndex('column_name','column_name','column_name','column_name')
    ->execute();
// PRIMARY => Must Before Do It Change auto_increment To Normal By Function Change
$mysqli->alter('table_name')
    ->dropIndex('primary')
    ->execute();
    
/* Alter Table Use Modify */
// -- Modify --
// column_name, data_type Is Required
// constraint, position Is Optional
// Modify Only One Column
$mysqli
    ->alter('table_name')
    ->modify('column_name','data_type','constraint','position')
    ->execute();
// Modify More Than One Column
$mysqli
    ->alter('table_name')
    ->modify('column_name','data_type','constraint','position')
    ->modify('column_name','data_type','constraint','position')
    ->execute();

/* Alter Table Use Change */
// -- Change --
// column_name, column_name_new, data_type Is Required
// constraint, position Is Optional
// Change Only One Column
$mysqli
    ->alter('table_name')
    ->change('column_name', 'column_name_new','data_type','constraint','position')
    ->execute();
// Change Only One Column
$mysqli
    ->alter('table_name')
    ->change('column_name', 'column_name_new','data_type','constraint','position')
    ->change('column_name', 'column_name_new','data_type','constraint','position')
    ->execute();

/* Alter Table Use Rename */
// -- Rename --
// Rename Only One Table
$mysqli
    ->alter('table_name')
    ->renameTable('table_name_new')
    ->execute();    

/* Select */
// one column --
$mysqli
    ->select()
    ->columns('column_name')
    ->table('table_name')
    ->execute()
    ->fetch();

// all columns --
$mysqli
    ->select()
    ->columns('*')
    ->table('table_name')
    ->execute()
    ->fetchAll();

// more than one column --
$mysqli
    ->select()
    ->columns(['column_name','column_name'])
    ->table('table_name')
    ->execute()
    ->fetchAll();

// features -- max(), min(), count(), sum()
// feature_name : max, min, count, sum
$mysqli
    ->select()
    ->feature('feature_name','column_name')
    ->table('table_name')
    ->execute()
    ->fetchAll();

/* Where */
// if row => fetch() ,, rows => fetchAll()
// operations {=,!=,<>,>,<,>=,<=}
$mysqli
    ->select()
    ->columns('*')
    ->table('table_name')
    ->where()
    ->operations('column_name','compare','value')
    ->execute()
    ->fetch();

// and
$mysqli
    ->select()
    ->columns('*')
    ->table('table_name')
    ->where()
    ->operations('column_name','compare','value')
    ->and()
    ->operations('id','compare','value')
    ->execute()
    ->fetchAll();

// or
$mysqli
    ->select()
    ->columns('*')
    ->table('table_name')
    ->where()
    ->operations('column_name','compare','value')
    ->or()
    ->operations('column_name','compare','value')
    ->execute()
    ->fetchAll();

// in
$mysqli
    ->select()
    ->columns('*')
    ->table('table_name')
    ->where()
    ->in('value','value','value','..')
    ->execute()
    ->fetchAll();

// Order By
// One
$mysqli
    ->select()
    ->columns('*')
    ->table('table_name')
    ->orderBy('ASC|DESC','column_name')
    ->execute()
    ->fetchAll();
    
// More Than One
$mysqli
    ->select()
    ->columns('*')
    ->table('table_name')
    ->orderBy('ASC|DESC','column_name','column_name','column_name','..')
    ->execute()
    ->fetchAll();

/* Group By */
// One
$mysqli
    ->select()
    ->columns('*')
    ->table('table_name')
    ->groupBy('column_name')
    ->execute()
    ->fetchAll();
    
// More Than One
$mysqli
    ->select()
    ->columns('*')
    ->table('table_name')
    ->groupBy('column_name','column_name','column_name','...')
    ->execute()
    ->fetchAll();

/* Limit */
$mysqli
    ->select()
    ->columns('*')
    ->table('table_name')
    ->limit('number')
    ->execute()
    ->fetchAll();

/* Insert */
$data = [
    'column_name' => 'value',
    'column_name' => 'value'
];

$mysqli
    ->insUp('insert into','table_name',$data)
    ->execute();

/* Update */
// Only On Record
$mysqli
    ->insUp('update','table_name',$data)
    ->where()
    ->operations('id','=',4)
    ->execute();
    
// All Records
$mysqli
    ->insUp('update','table_name',$data)
    ->execute();
    
// delete
// one record
$mysqli
    ->delete('table_name')
    ->where()
    ->operations('column_name','compare','value')
    ->execute();
    
// all record
$mysqli
    ->delete('table_name')
    ->execute();

/* Add Primary Key */
$mysqli
    ->alter('users')
    ->add_primary_key('column_name')
    ->execute();

/* Add Foreign Key */
$mysqli
    ->alter('users')
    ->add_foreign_key('column_name','table','table_primary_key')
    ->execute();

/* Join && inner join */
$mysqli
    ->select()
    ->columns('`table1`.*,`table2`.*')
    ->table('table1')
    ->join('table2')
    ->on('table1','table1_id','table2','id')
    ->execute()
    ->fetchAll();

/* rightJoin */
$mysqli
    ->select()
    ->columns('`table1`.*,`table2`.*')
    ->table('table1')
    ->rightJoin('table2')
    ->on('table1','table1_id','table2','id')
    ->execute()
    ->fetchAll();
    
 /* leftJoin */   
$mysqli
    ->select()
    ->columns('`table1`.*,`table2`.*')
    ->table('table1')
    ->leftJoin('table2')
    ->on('table1','table1_id','table2','id')
    ->execute()
    ->fetchAll();

```
