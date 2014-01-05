<?php
class db_manager
{
    protected $con;

    public function __construct($mysql_database, $mysql_host, $mysql_username, $mysql_password, $charset='utf8')
    {
        $dbConnection = new PDO('mysql:dbname=' . $mysql_database.  ';host=' . $mysql_host . ';charset=' . $charset , $mysql_username, $mysql_password);
        $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->con = $dbConnection;
    }

    public function execute($sql, $params = array())
    {
        $stmt = $this->con->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }

    public function fetch($sql, $params = array())
    {
        return $this->execute($sql, $params)->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchAll($sql, $params = array())
    {
        return $this->execute($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function __destruct()
    {
        unset($this->con);
    }
}
?>