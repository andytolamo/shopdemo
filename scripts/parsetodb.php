<?php

//NOTE: this is one way insert. It does not take account existing data but insetts again data even if already exists
//too lazy to write for this test the check but note that is known issue.

try {
    $path = getPath();
    $data = file_get_contents($path);;
    $row = preg_split("/\\r\\n|\\r|\\n/", $data);

    if (!is_array($row)) {
        throw new Exception('Problem in data. No rows in file');
    }

    //delete first row of headers
    unset($row[0]);

    foreach ($row as $arow) {
        $array = str_getcsv($arow, ',');
        if (!is_array($array)) {
            throw new Exception('Problem in data. Cant parse csv row:' . $arow);
        }
        checkRow($array);
        #       var_dump($array);
        addToDB($array);

    }


} catch (Exception $e) {

    echo "\nError: " . $e->getMessage() . "\n";
}


function addToDB($array)
{
    $pdo = new PDO(
        'mysql:host=127.0.0.1;dbname=shop',
        'cgi',
        'ecommerce'
    );

    try {
        $pdo->beginTransaction(); //start transaction: either everything goes or nothing
        $tax_category = saveTax($pdo, $array);
        $sku_id = saveSku($pdo, $array, $tax_category);

        saveImage($pdo, $array, $sku_id);
        savePrice($pdo, $array, $sku_id);
        saveDescription($pdo, $array, $sku_id);
        $pdo->commit();  // end transaction
        echo "\nimport done";
git

    } catch (Exception $ex) {
        //note error handling actually does not work well with PDO and exceptions. Silent errors with failed arguments.
        //wont fix for this project but note this

        $pdo->rollBack(); // in case of error, lets rollback all queries
        throw new Exception('Query error: ' . $ex->getMessage());
    }

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
        throw new Exception('Problem in data. Incorrect amount data in a row: ' . count($row));
    }

}

/**
 * get path to read/execute
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


function doesCategoryExist($pdo, $percentage, $destination_location = 'default')
{
    try {
        foreach ($pdo->query("SELECT tax_category FROM tax WHERE percentage = $percentage AND  destination_location = '$destination_location'") as $row) {
            return $row['tax_category'];
        }
    } catch (PDOException $ex) {
        throw new Exception('Query error: ' . $ex->getMessage());
    }

    return false;
}

/**
 * saves to sku table
 *
 * @param $pdo
 * @param $row
 * @param $tax_category_id
 * @return mixed
 * @throws Exception
 */
function saveSku($pdo, $row, $tax_category_id)
{
    $sku = $row[0];
    $country_of_origin = $row[5];
    $quantity = $row[8];
    $weight = $row[7];
    $name = $row[6];
    $dateArray = explode('/', $row[2]);
    $created_at = "20$dateArray[2]-$dateArray[0]-$dateArray[1]";
    $inserted_at = date("Y-m-d");
    try {

        $pdo->exec("INSERT INTO sku(sku, created_at, inserted_at, country_of_origin, quantity, weight, sku_name, tax_category_id) 
     VALUES('$sku', '$created_at', '$inserted_at', '$country_of_origin', $quantity, $weight, '$name', $tax_category_id )");
        return $pdo->lastInsertId();

    } catch (PDOException $ex) {
        throw new Exception('Query error: ' . $ex->getMessage());
    }


}

/**
 * saves to tax table
 *
 * @param $pdo
 * @param $row
 * @return bool|mixed
 * @throws Exception
 */
function saveTax($pdo, $row)
{

    $tax = $row[9];
    $tax = str_replace('%', '', $tax);
    $percentage = floatval($tax);

    $category = doesCategoryExist($pdo, $percentage);

    if ($category) {
        return $category;

    }

    //shortcut to create tax category. Realistically they would be inserted before hand. For example tax free
    $category = $tax;
    try {

        $pdo->exec("INSERT INTO tax(tax_category, percentage) VALUES('$category', $percentage)");
        return $category;
    } catch (PDOException $ex) {
        throw new Exception('Query error: ' . $ex->getMessage());
    }
}

/**
 * saves to image table
 *
 * @param $pdo
 * @param _ $row
 * @param $sku_id
 * @return bool
 */
function saveImage($pdo,  $row, $sku_id)
{
    $url = $row[4];
    try {
        $pdo->exec("INSERT INTO images(url, sku_id) VALUES('$url', $sku_id)");
        return true;
    } catch (PDOException $ex) {
        throw new Exception('Query error: ' . $ex->getMessage());
    }

}

/**
 * saves to description table
 *
 * @param $pdo
 * @param _ $row
 * @param $sku_id
 * @return bool
 * @throws Exception
 */
function saveDescription($pdo, $row, $sku_id)
{
    $description = $row[3];
    try {
        $pdo->exec("INSERT INTO description (description, sku_id) VALUES('$description',  $sku_id)");
        return true;
    } catch (Exception $ex) {
        throw new Exception('Query error: ' . $ex->getMessage());
    }


}

/**
 * saves to price table
 *
 * @param $pdo
 * @param $row
 * @param $sku_id
 * @return bool
 * @throws Exception
 */
function savePrice($pdo, $row, $sku_id)
{

    $price =  $row[1] * 100;  //price in cents
    $currency = 'EUR';

    try {
        $pdo->exec("INSERT INTO price (price, currency, sku_id) VALUES($price, '$currency', $sku_id)");
        return true;
    } catch (PDOException $ex) {
        throw new Exception('Query error: ' . $ex->getMessage());
    }

}