<html lang="">
<head>
    <title>PHP Test</title>
</head>
<body>
</body>
</html>

<?php

function abbreviateMonths($issues)
{
    $months = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
    foreach ($months as $month) {
        if (strpos($issues, $month)) {
            return str_replace($month, substr($month, 0, 3), $issues);
        }
    }
    return $issues;
}

$file = fopen("Index_TWHS_Newsletters.csv", "r");
$cleanFile = fopen("CleanIndex.csv", "w");

while (!feof($file)) {
    $row = fgetcsv($file);
    if ($row == NULL) {
        continue;
    } // each line on excel generated csv file has lf, so last row is empty

    $subject = $row[0];
    $issues = $row[1];
    $notes = $row[2];

    $year = substr($issues, 0, 4);

    if (!is_numeric($year)) {
        if (rtrim($year) == "see") {
            fputcsv($cleanFile, array($subject, $issues, "", $notes));

        }
        continue;
    }

    $issues = str_replace("  ", " ", $issues); // replace double spaces with space

    $issues = abbreviateMonths($issues);

    $issues = preg_replace('/(\s\d{1,2});\s*(\d{1,2}\s)/', '$1, $2', $issues);

    $issues = preg_replace('/(\d{1,2})\s(\d{4})/', '$1; $2', $issues);
    $issues = preg_replace('/(\d{1,2}),\s(\d{4})/', '$1; $2', $issues);

    $issues = preg_replace('/(-*\d{1,2})\s*(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)/', '$1; $2', $issues);
    $issues = preg_replace('/(-\d{1,2}),\s(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)/', '$1; $2', $issues);

    $issues = preg_replace('/(\s\d{1,2})\s*:\s(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)/', '$1; $2', $issues);
    $issues = preg_replace('/(\s\d{1,2}),\s(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)/', '$1; $2', $issues);

    $issues = preg_replace('/(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\s(\d{4})/', '$1; $2', $issues);

    $issues = preg_replace('/(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec),\s/', '$1 ', $issues);


    $splitIssues = str_getcsv($issues, ';');

    foreach ($splitIssues as $issue) {
        $issue = ltrim($issue);
        $currentYear = substr($issue, 0, 4);
        if (!is_numeric($currentYear)) {
            $currentYear = $year;
            $issue = $year . ": " . $issue;
        }
        $page = ltrim(substr($issue, 9));

        fputcsv($cleanFile, array($subject, substr($issue, 0, 9), $page, $notes));
        $year = $currentYear;
    }
}
fclose($cleanFile);
fclose($file);
