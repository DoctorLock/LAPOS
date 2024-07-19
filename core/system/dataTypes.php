<?php
enum DataTypes: string
{
    case String = "VARCHAR(512)";
    case INTEGER = "INT";
    case FLOAT = "FLOAT";
    case BOOLEAN = "BOOLEAN";
    case DATETIME = "DATETIME(5)";
}
