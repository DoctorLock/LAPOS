<?php
require_once(__DIR__ . "/../dataTypes.php");
class column
{
    public string $name;
    public DataTypes $type;
    public bool $allowNull = true;
    public $defaultValue = null;
    public ?string $fkReference = null;
    public $owner;
    /**
     * __construct
     *
     * @param string $name the name of the equivalent property in SQL
     * @param DataTypes $phpType the Datatype in PHP
     * @param mixed $defaultValue the default value if none is provided
     */
    function __construct(string $name, DataTypes $type, $table = null, bool $allowNull = true, $defaultValue = null,  string $fkReference = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->defaultValue = $defaultValue;
        $this->allowNull = $allowNull;
        $this->owner = $table;
        $this->fkReference = $fkReference;
    }
}
?>