<html lang="">
<head>
    <title>PHP Test</title>
</head>
<body>
<?php
$file = fopen("Index_TWHS_Newsletters.csv", "r");

while (!feof($file)) {
    $row = fgetcsv($file);
    if (!is_numeric(substr($row[1], 0, 4))) { continue; }
    print_r($row[1] . '<br />');
}

fclose($file);
echo '<p>Hello World</p>'; ?>
</body>
</html>