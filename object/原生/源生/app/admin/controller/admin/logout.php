<?php

unset($_SESSION['userinfo']);
echo "<script> alert ('退出成功');</script>";
header("Refresh:1;url=index.php?m=admin&c=admin&a=login");


?>