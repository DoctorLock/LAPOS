<?php
require_once(__DIR__ . "/sqlObject.php");
abstract class Person extends sqlObject
{
    public string $name;
    public string $phoneNumber;
    public string $email;

    public function __construct(string $name, string $phoneNumber = "", string $email = "", $id = null)
    {
        $this->name = $name;
        $this->phoneNumber = $phoneNumber;
        $this->email = $email;
        $this->id = $id;
    }
}
