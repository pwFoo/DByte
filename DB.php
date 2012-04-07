<?php
/**
 * Provides a database wrapper around the PDO service to help reduce the effort
 * to interact with a RDBMS such as SQLite, MySQL, and PostgreSQL data source.
 *
 * 		DB::$c = new PDO($dsn);
 *
 * @package		MicroMVC
 * @author		David Pennington
 * @copyright	(c) 2011 MicroMVC Framework
 * @license		http://micromvc.com/license
 ********************************** 80 Columns *********************************
 */
define('N',NULL);

class DB
{
	static $q,$c,$p,$i = '`';

	/**
	 * Fetch a column offset from the result set (COUNT() queries)
	 *
	 * @param string $query query string
	 * @param array $params query parameters
	 * @param integer $key index of column offset
	 * @return array|null
	 */
	static function column($query, $params = N, $key = 0)
	{
		if($statement = DB::query($query, $params))
			return $statement->fetchColumn($key);
	}

	/**
	 * Fetch a single query result row
	 *
	 * @param string $query query string
	 * @param array $params query parameters
	 * @return mixed
	 */
	static function row($query, $params = N)
	{
		if($statement = DB::query($query, $params))
			return $statement->fetch();
	}

	/**
	 * Fetches an associative array of all rows as key-value pairs (first
	 * column is the key, second column is the value).
	 *
	 * @param string $query query string
	 * @param array $params query parameters
	 * @return array
	 */
	static function pairs($query, $params = N)
	{
		$data = array();

		if($statement = DB::query($query, $params))
			while($row = $statement->fetch(\PDO::FETCH_NUM))
				$data[$row[0]] = $row[1];

		return $data;
	}

	/**
	 * Fetch all query result rows
	 *
	 * @param string $query query string
	 * @param array $params query parameters
	 * @param int $column the optional column to return
	 * @return array
	 */
	static function fetch($query, $params = N, $column = N)
	{
		if( ! $statement = DB::query($query, $params)) return;

		// Return an array of records
		if($column === N) return $statement->fetchAll();

		// Fetch a certain column from all rows
		return $statement->fetchAll(\PDO::FETCH_COLUMN, $column);
	}

	/**
	 * Prepare and send a query returning the PDOStatement
	 *
	 * @param string $query query string
	 * @param array $params query parameters
	 * @return object|null
	 */
	static function query($query, $params = N)
	{
		$statment = static::$c->prepare(DB::$q[] = strtr($query, '`', DB::$i));
		$statment->execute($params);
		return $statment;
	}

	/**
	 * Insert a row into the database
	 *
	 * @param string $table name
	 * @param array $data
	 * @return integer|null
	 */
	static function insert($table, array $data)
	{
		$query = "INSERT INTO`$table`(`" . implode('`,`', array_keys($data))
			. '`)VALUES(' . rtrim(str_repeat('?,', count($data = array_values($data))), ',') . ')';
		return DB::$p
			? DB::column($query . 'RETURNING`id`', $data)
			: (DB::query($query, $data) ? static::$c->lastInsertId() : N);
	}

	/**
	 * Update a database row
	 *
	 * @param string $table name
	 * @param array $data
	 * @param array $w where conditions
	 * @return integer|null
	 */
	static function update($table, $data, $value, $column = 'id')
	{
		$keys = implode('`=?,`', array_keys($data));
		if($statment = DB::query(
			"UPDATE`$table`SET`$keys`=?WHERE`$column`=?",
			array_values($data + array($value))
		))
			return $statment->rowCount();
	}
}