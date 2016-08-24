<?php
require_once __DIR__ . '../vendor/autoload.php';

try {
    $path = getPath();
    $data = file_get_contents($path);;
    $row =  preg_split("/\\r\\n|\\r|\\n/", $data);

    if (!is_array($row)) {
        throw new Exception('Problem in data. No rows in file');
    }

    //delete first row of headers
    unset($row[0]);

    foreach ($row as $arow) {
        $array = str_getcsv($arow, ',');
        if(!is_array($array)){
            throw new Exception('Problem in data. Cant parse csv row:'.$arow);
        }
        checkRow($array);
        var_dump($array);


    }



} catch (Exception $e) {

    echo "\nError: " . $e->getMessage() . "\n";
}


/**
 * check csv has correct amount of parameters
 *
 * @param $row
 * @throws Error
 */
function checkRow($row)
{
    if (count($row) != 10) {
        throw new Exception('Problem in data. Incorrect amount data in a row: '.count($row));
    }

}

/**
 * get path to read
 *
 * @return string
 */
function getPath()
{
    $path = getcwd();
    if (!strpos($path, 'scripts')) {
        $path .= '/scripts';
    }

    $path .= '/sample-data.csv';
    return $path;
}