<?php

$debug = $_GET;

?>

<!DOCTYPE html>
<html lang="en">
<body>
<?php

foreach ($debug as $key => $value){
    echo $key;
    echo " ";
    echo $value;
    echo "  ";
}

?>
</body>
</html>
