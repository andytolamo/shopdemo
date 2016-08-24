<?php

require_once __DIR__ . '/vendor/autoload.php';


$app = new Silex\Application();

//regiest twig component
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/views',
));


$app->get('/', function () use ($app) {

    try {

        $products = getAllProducts();
        return $app['twig']->render('products.twig', array(
            'products' => $products,
        ));
    } catch (Exception $e) {
        return $app['twig']->render('error.twig', array(
            'error_msg' => $e->getMessage(),
        ));
    }
});

$app->run();


/**
 * @return array
 * @throws Exception
 */
function getAllProducts()
{
    require('settings/settings.php');
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=shop",
        $db_user,
        $db_passwd
    );

    try {
        return getAllSku($pdo);
    } catch (Exception $e) {
        throw $e;
    }
}

/**
 * gets all products and returns composition all related
 *
 * @param $pdo
 * @return array
 * @throws Exception
 */
function getAllSku($pdo)
{
    $result = array();
    $finalResult = array();
    try {
        foreach ($pdo->query('SELECT * FROM sku') as $row) {
            array_push($result, $row);
        }

        foreach ($result as $row) {
            $tax_percentage = getTaxRate($pdo, $row['tax_category_id']);
            if ($tax_percentage === false) {
                throw new Exception('did not get percentage:' . $row['tax_category_id']);

            }
            $price = getPrice($pdo, $row['id']);
            if ($price === false) {
                throw new Exception('did not get price.Sku_id:' . $row['id']);

            }

            $image = getImage($pdo, $row['id']);
            if ($image === false) {
                throw new Exception('did not get image.Sku_id:' . $row['id']);
            }

            $description = getDescription($pdo, $row['id']);
            if ($description === false) {
                throw new Exception('did not get description.Sku_id:' . $row['id']);
            }

            $row['tax_percentage'] = $tax_percentage;
            $row['price'] = number_format(floatval($price / 100), 2); // change to main currenc + get float and decimals
            $row['image'] = $image;
            $row['description'] = $description;

            array_push($finalResult, $row);
        }


    } catch (Exception $e) {
        throw new Exception($e->getMessage());
    }

    return $finalResult;

}

/**
 * return tax rate
 *
 * @param $pdo
 * @param $category_id
 * @return mixed
 * @throws Exception
 */
function getTaxRate($pdo, $category_id)
{
    try {
        foreach ($pdo->query("SELECT percentage FROM tax WHERE tax_category = '$category_id' AND  destination_location = 'default'") as $row) {
            return $row['percentage'];
        }


    } catch (Exception $e) {
        throw new Exception($e->getMessage());
    }

    return false;
}

/**
 * gets price in Euros
 *
 * @param $pdo
 * @param $sku_id
 * @return bool
 * @throws Exception
 */
function getPrice($pdo, $sku_id)
{
    {
        try {
            foreach ($pdo->query("SELECT price FROM price WHERE sku_id = $sku_id 
            AND  currency = 'EUR'") as $row) {
                return $row['price'];
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return false;
    }
}

/**
 * gets single image. By slight altering could return  multiple.
 *
 * @param $pdo
 * @param $sku_id
 * @return bool
 * @throws Exception
 */
function getImage($pdo, $sku_id)
{
    {
        try {
            foreach ($pdo->query("SELECT url FROM images WHERE sku_id = $sku_id") as $row) {
                return $row['url'];
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return false;
    }
}


/**
 * gets single description in english
 *
 * @param $pdo
 * @param $sku_id
 * @return bool
 * @throws Exception
 */
function getDescription($pdo, $sku_id)
{
    {
        try {
            foreach ($pdo->query("SELECT description FROM description WHERE sku_id = $sku_id AND lang = 'eng'") as $row) {
                return $row['description'];
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return false;
    }
}
