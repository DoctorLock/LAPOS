<?php
require_once(__DIR__ . "/sqlObject.php");
require_once(__DIR__ . "/Customer.php");
require_once(__DIR__ . "/table.php");
require_once(__DIR__ . "/sqlObject.php");
/**
 * Reservation
 * @param INT $customerId
 */
class Reservation extends sqlObject //ALL database objects need to extend from sqlObject
{
    public function __construct($customerId, $table, $bookingTime, $id = null){
        $this->customerId = $customerId;
        $this->table = $table;
        $this->bookingTime = new DateTime($bookingTime);
        $this->id = $id;
    }
    //MUST define a name of the sql table
    //note the "_" at the start of the variable. This denotes that it will NOT be backed up to SQL. 
    public static string $_sqlTable = "Reservations";
    //this function defines all foreign key relations. NOTE: FKs are constrained so that you can only like a column inside of an object
    //to the Id of another object. 
    public static function fkColumns(): array
    {
        return 
        [
            "customerId" => Customer::Class(),
            "table" => Table::Class()
        ];
    }

    //lack of _ means these variables will be backed up to database
    //each must have a type defined. 
    //Add a "?" at the start to denote it can be null
    //The primary key ID is automatically defined in the sqlObject abstract class
    public int $customerId;
    public int $table;
    public DateTime $bookingTime;
}
