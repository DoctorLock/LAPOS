<?php
require_once(__DIR__ . "/../system/systemObjects/column.php");
/**
 * Undocumented class
 */
abstract class sqlObject
{
    //defines the 
    public ?int $id = null;
    //the name of the SQL table
    public static string $_sqlTable = "";
    public static function fkColumns(): array
    {
        return [];
    }

    /**
     * Class
     *  returns Late Static Binding classname string
     * @return string
     */
    public static function Class()
    {
        return get_called_class();
    }
    /**
     * createColumn
     * create a column object owned by the current class
     * @param string $name
     * @param DataTypes $type
     * @param boolean $allowNullcolumns
     * @param [type] $defaultValue
     * @param column|null $fkReference
     * @param column|null $fkReference
     * @return void
     */
    public static function createColumn(string $name, DataTypes $type, bool $allowNull = true, $defaultValue = null,  string $fkReference = null)
    {
        return new column($name,  $type, get_called_class(),  $allowNull, $defaultValue,  $fkReference);
    }
    /**
     * getColumns
     * returns the columns associated with the object
     * @return array
     */
    public static function getColumns()
    {
        $reflectionClass = new ReflectionClass(get_called_class());
        $properties = $reflectionClass->getProperties();
        $cols = [];
        $fks = get_called_class()::fkColumns();
        // Loop through each property
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            if (substr($propertyName, 0, 1) != "_") {
                $propertyType = null;
                $type = DataTypes::String;
                $propertyType = $property->getType();
                $allowNull = false;
                if (substr($propertyType, 0, 1) == "?") {
                    $propertyType = ltrim($propertyType, "?");
                    $allowNull = true;
                }
                $defaultValue = $property->getDefaultValue();

                switch ($propertyType) {
                    case "int":
                        $type = DataTypes::INTEGER;
                        break;
                    case "DateTime":
                        $type = DataTypes::DATETIME;
                        break;
                    case "float":
                        $type = DataTypes::FLOAT;
                        break;
                    case "bool":
                        $type = DataTypes::BOOLEAN;
                        break;
                    default:
                        $type = DataTypes::String;
                        break;
                }
                $fk = null;
                if (array_key_exists($propertyName, $fks)) {
                    $fk =  $fks[$propertyName];
                }
                if ($fk == null) {
                    $cols[$propertyName] =  static::createColumn($propertyName, $type, $allowNull,  $defaultValue, null);
                } else {
                    $cols[$propertyName] =  static::createColumn($propertyName, $type, $allowNull,  $defaultValue, $fk::Class());
                }
            }
        }
        return $cols;
    }
}
