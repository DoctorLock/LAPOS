<?php
    require_once("./core/system/sql.php");

    if(substr(phpversion(),0,3)>="8.2"){
        echo "<h2 style='color:green'>PHP version above 8.2!</h2>";
    }else{
        echo "<h2 style='color:red'>PHP version below 8.2! Please re-install XAMPP with correct PHP Version</h2>";
    }
    if(file_exists("./core/config/sqlConfig.php")){
        echo "<h2 style='color:green'>Config File found!</h2>";
    }else{
        echo "<h2 style='color:red'>Config File Not found!</h2>";
    }
    sql::sqlTest();    



    
?>