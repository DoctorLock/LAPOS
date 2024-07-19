<?php
require_once(__DIR__ . "/dataTypes.php");
include_once(__DIR__ . "/../config/sqlConfig.php");
/**
 * sql
 * the sql class handles all communication between the web application and the database
 */
class sql
{

    private $conn;

    function __construct()
    {
        $this->conn = new mysqli(DATABASE_SERVER, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
    }
    /**
     * sqlTest
     * used on test.php to test SQL config
     * @return void
     */
    public static function sqlTest()
    {
        if ($conn = new mysqli(DATABASE_SERVER, DATABASE_USER, DATABASE_PASSWORD)) {
            echo ("<h2 style='color:green'>SQL connection successfull!</h2>");
            $query = "SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = '" . DATABASE_NAME . "'";

            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                echo "<h2 style='color:green'>Succesfully connected to database!</h2>";
            } else {
                echo "<h2 style='color:red'>Database " . DATABASE_NAME . " does not exist</h2>";
            }
        } else {
            echo ("<h2 style='color:red'>SQL connection failed!</h2>");
        }
    }
    /**
     * run an SQL query
     *
     * @param string $qry
     * @return ?array results of query
     */
    public function sqlQry(string $qry)
    {
        $sqlResult = $this->conn->query($qry);
        $result = [];
        while ($row = $sqlResult->fetch_assoc()) {
            $result[] = $row;
        }
        return $result;
    }
    /**
     * getObjects
     * Returns an array of objects based on SQL data.
     * @param string $class the class to build
     * @param string $WhereClause a SQL where clause to filter data
     * @return array|null
     */
    public function getObjects(string $class, string $WhereClause = ""): ?array
    {
        $results = [];
        if ($this->conn) {
            $table = $class::$_sqlTable;
            $sql = "SELECT * FROM `" . $table . "`";
            if ($WhereClause != "") {
                $sql .= " " . $WhereClause;
            }
            $sql .= ";";
            $result = $this->conn->query($sql);
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            foreach ($rows as $row) {
                $reflectionClass = new ReflectionClass($class);
                $constructorParams = $reflectionClass->getConstructor()->getParameters();
                $args = array();
                foreach ($constructorParams as $param) {
                    $paramName = $param->getName();
                    if (isset($row[$paramName])) {
                        $args[] = $row[$paramName];
                    } else {
                        // If parameter not found in the array, you can handle it as per your requirement.
                        // Here, I'm just passing null.
                        $args[] = null;
                    }
                }
                $results[] = $reflectionClass->newInstanceArgs($args);
            }
        }
        return $results;
    }
    /**
     * TableNameFromClassString
     * takes in the class name in the form a string, gets the SQL table name of said class.
     * @param string $className
     * @return string
     */
    private function TableNameFromClassString(string $className): string
    {
        $reflectionClass = new ReflectionClass($className);

        return $reflectionClass->getStaticPropertyValue("_sqlTable");
    }
    /**
     * buildTableFromObject
     * Generates code to build a table based on the object. 
     * Ensure that your object has all columns defined in the getColumns() function
     * @param string $object
     * @return string
     */
    public function buildTableFromObject(string $object): string|bool
    {
        if ($this->conn) {
            $sql = "";
            $table = $object::$_sqlTable;
            $cols = $object::getColumns();

            $sql .= "CREATE TABLE IF NOT EXISTS `$table` (";
            $fk = "";
            foreach ($cols as $col) {
                $sql .= "`" . $col->name . "` " . $col->type->value . " " . ((!$col->allowNull) ? " NOT NULL" : "") . (($col->name == "id") ? " AUTO_INCREMENT" : "") . ", ";
                if ($col->fkReference != null) {
                    $fk .= "FOREIGN KEY (`" . $col->name . "`) REFERENCES " . $this->TableNameFromClassString($col->fkReference) . "(id),";
                }
            }
            $sql .= "PRIMARY KEY (Id)";
            if ($fk != "") {
                $sql .= ", " . rtrim($fk, ",");
            }
            $sql .= ");";
            return mysqli_query($this->conn, $sql);
        } else {
            return "connection failed";
        }
    }
    /**
     * saveObject
     *  Pass an sqlObject in, if the object has an ID, the related record in the database will be updated
     *  if ID is null, inserts the object as a new row in the related table
     * @param sqlObject $object
     * @return bool
     */
    public function saveObject(sqlObject $object)
    {
        $table = $object::$_sqlTable;
        $cols = $object->getColumns();
        $id = $object->id;
        $sql = "";

        if ($id == null) {
            //create record
            $sql = "INSERT INTO $table(";
            $values = "VALUES(";
            foreach ($cols as $col) {
                if ($col->name != "id") {
                    $sql .= "`" . $col->name . "`,";
                    if ($col->type == DataTypes::String) {
                        $val = "'" .  $object->{$col->name} . "'";
                    } else if ($col->type == DataTypes::DATETIME) {
                        $val = "'" .  $object->{$col->name}->format("Y-m-d H:i") . "'";
                    } else {
                        $val = $object->{$col->name};
                    }
                    $values .= $val . ",";
                }
            }
            $sql = rtrim($sql, ",") . ") " . rtrim($values, ",") . ");";
            mysqli_query($this->conn, $sql);
            $object->id = $this->conn->insert_id;
            return true;
        } else {
            $sql = "UPDATE $table SET ";
            foreach ($cols as $col) {
                if ($col->name != "id") {
                    $sql .= $col->name . " = ";
                    if ($col->type == DataTypes::String || $col->type == DataTypes::DATETIME) {
                        $val = "'" . $object->{$col->name} . "'";
                    } else {
                        $val = $object->{$col->name};
                    }
                    $sql .= $val . ", ";
                }
            }
            $sql = rtrim($sql, ", ") . " WHERE id = " . $object->id;
            mysqli_query($this->conn, $sql);
            return true;
        }
        return false;
    }
}
