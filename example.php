<?php die('not for direct access');

// So our print_r's look good :)
header('Content-Type: text/plain');

// Simple separator
function br(){print "\n" . str_repeat('-',50) . "\n";}

// We need this!
require('vendor/autoload.php');

use \DByte\DB;

/*
 * Connect to Database
 */


// Create a new PDO connection to MySQL
$pdo = new PDO(
	'mysql:dbname=databasename;host=localhost',
	'root',
	'',
	array(
		PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
	)
);

// Create a new DB class object
$db = new DB($pdo);

/*
 * CRUD Queries
 */

// Create a test table
$db->query("DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(70) NOT NULL,
  `created` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");


// Insert some users
$user = array(
	'username' => 'Mary',
	'email' => 'mary@example.org',
	'created' => time()
);
$result = $db->insert('users', $user);
var_dump($result);


$user = array(
	'username' => 'John',
	'email' => 'john@example.com',
	'created' => time()
);
$result = $db->insert('users', $user);
var_dump($result);


$user = array(
	'username' => 'Bobb',
	'email' => 'bobb@example.com',
	'created' => time()
);
$result = $db->insert('users', $user);
var_dump($result);


$user = array(
	'username' => 'Troll',
	'email' => 'icanhaz@email.com',
	'created' => time()
);
$result = $db->insert('users', $user);
var_dump($result);


// Oops! We got Bob's info wrong! Lets fix it!
$user = array(
	'username' => 'Bob',
	'email' => 'bob@example.com',
);
$result = $db->update('users', $user, 3);
var_dump($result);


// Our moderators say that the "Troll" user needs to be deleted!
$result = $db->query('DELETE FROM users WHERE username = ?', array('Troll'));
var_dump($result);


/*
 * Select Queries
 */


// Count all the users
$result = $db->column('SELECT COUNT(*) FROM `users`');
var_dump('Total users: '. $result);
br();


// Get user number 2 (John)
$result = $db->row('SELECT * FROM `users` WHERE id = ?', array(3));
var_dump($result);
br();


// Fetch all the users!
$result = $db->fetch('SELECT * FROM `users`');
var_dump($result);
br();


// Fetch users from "example.com"
$result = $db->fetch('SELECT * FROM `users` WHERE email LIKE ?', array('%example.com'));
var_dump($result);
br();


/*
 * Results
 */


print count($db->queries) . " Queries Run:\n";
print_r($db->queries);
