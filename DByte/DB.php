<?php
namespace DByte;
class DB {
    private $conn   = null;
    private $marks  = '`';
    private $pgsql  = false;
    public $queries  = array();

    public function __construct($pdo) {
        $this->conn = $pdo;
        
        switch ($pdo->getAttribute(\PDO::ATTR_DRIVER_NAME)) {
            case "mysql": 
                $this->marks = '`';
                $this->pgsql = false;
                break;
            case "sqlite":
                $this->marks = '"';
                $this->pgsql = false;
                break;
            case "pgsql":
                $this->marks = '"';
                $this->pgsql = true;
                break;
        }
    }   

	/**
	 * Fetch a column offset from the result set (COUNT() queries)
	 *
	 * @param string $query query string
	 * @param array $params query parameters
	 * @param integer $key index of column offset
	 * @return array|null
	 */
	public function column($query, $params = NULL, $key = 0)
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
	public function row($query, $params = NULL)
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
	public function pairs($query, $params = NULL)
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
	public function fetch($query, $params = NULL, $column = NULL)
	{
		if( ! $statement = DB::query($query, $params)) return;

		// Return an array of records
		if($column === NULL) return $statement->fetchAll();

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
	public function query($query, $params = NULL)
	{
        //$statement = static::$c->prepare(DB::$q[] = strtr($query, '`', DB::$i));
        $statement = $this->conn->prepare($this->queries[] = $query);
		$statement->execute($params);
		return $statement;
	}

	/**
	 * Insert a row into the database
	 *
	 * @param string $table name
	 * @param array $data
	 * @return integer|null
	 */
	public function insert($table, array $data)
	{
		$query = "INSERT INTO {$this->marks}$table{$this->marks} ({$this->marks}" . implode("{$this->marks}, {$this->marks}", array_keys($data))
			. "{$this->marks}) VALUES (" . rtrim(str_repeat('?, ', count($data = array_values($data))), ', ') . ')';
		return $this->pgsql
			? DB::column($query . " RETURNING {$this->marks}id{$this->marks}", $data)
			: (DB::query($query, $data) ? static::$c->lastInsertId() : NULL);
	}

	/**
	 * Update a database row
	 *
	 * @param string $table name
	 * @param array $data
	 * @param array $w where conditions
	 * @return integer|null
	 */
	public function update($table, $data, $value, $column = 'id')
	{
		$keys = implode("{$this->marks}=?,{$this->marks}", array_keys($data));
		if($statement = DB::query(
			"UPDATE {$this->marks}$table{$this->marks} SET {$this->marks}$keys{$this->marks} = ? WHERE {$this->marks}$column{$this->marks} = ?",
			array_values($data + array($value))
		))
			return $statement->rowCount();
	}
}
