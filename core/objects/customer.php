<?php
require_once(__DIR__ . "/person.php");
require_once(__DIR__ . "/../system/sql.php");
class Customer extends Person
{
    public static string $_sqlTable = "Customers";
    public function __construct(string $name, string $phoneNumber = '', string $email = '', int $id = null)
    {
        parent::__construct($name, $phoneNumber, $email, $id);
    }

}
