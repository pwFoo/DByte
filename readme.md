# DByte

### A 1kB PHP database layer for SQLite, PostgreSQL, and MySQL

> DByte is built ontop of PDO to provide a level of query abstraction missing
> from the default PDO object. DByte uses 100% prepared statements.

Many database layers seem to exclude some of the most basic retrieval methods.
Often databases just default to using `fetchAll` for everything and then extract
the single row, column, array, or object they need.

However, when you query a database you generally want a certain type of result back.

### I want a single column

	$count = $db->$queriescolumn('SELECT COUNT(*) FROM `user`);

### I want an array(key => value) results (i.e. for making a selectbox)

	$pairs = $db->$queriespairs('SELECT `id`, `username` FROM `user`);

### I want a single row result

	$user = $db->$queriesrow('SELECT * FROM `user` WHERE `id` = ?', array($user_id));

### I want an array of results (even an empty array!)

	$banned_users = $db->$queriesfetch('SELECT * FROM `user` WHERE `banned` = ?, array(TRUE));

### I want to insert a new record

	$db->$queriesinsert('user', $array);

### I want to update a record

	$db->$queriesupdate('user', $array, $user_id);

### I want to delete a record

	$db->$queriesquery('DELETE FROM `user` WHERE `id` = ?', array($user_id));

# Notes / Advanced Usage

In order to work across all databases it's recommended that you use the tilde
(\`) character in all your queries to quote column/table names. This character
will be replaced in your query with the correct quoted identifier at run time.

> DO NOT USE THE `DB.min.php` FILE! It is only included to show that the file
> actually is 1024 characters. Unlike Javascript, you gain no performance by using it!

## Composer Install

The easiest way to install DByte is [to use composer](https://getcomposer.org/).

	curl -s http://getcomposer.org/installer | php

Then create a `composer.json` file in your root directory and include this inside it.

	{
		"require": {
			"xeoncross/dbyte" : "dev-master"
		}
	}

With composer installed (and your `composer.json` file created) you can then run
composer to install DByte into a "vendors" folder..

	php composer.phar install

which you can include in your PHP scripts...

	require 'vendor/autoload.php';

### Simple Install

Or you can just [download the file](https://github.com/Xeoncross/DByte/archive/master.zip)
and then include it in your scripts.

	require('DByte\DB.php');

## Setup

To begin using the DB object you need to assign a PDO connection object.

	// Create a new PDO connection to MySQL
	$pdo = new PDO(
		'mysql:dbname=yourdatabase;host=localhost',
		'root',
		'',
		array(
			PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		)
	);

	use \DByte\DB; // or class_alias('\DByte\DB', 'DB');
	$db = new DB($pdo);


## How can I see what queries have run?

	print_r($db->queries);
