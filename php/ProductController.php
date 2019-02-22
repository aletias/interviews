<?php

namespace Realmdigital\Web\Controller;

use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Silex\Application;

/**
 * @SLX\Controller(prefix="product/")
 */
class ProductController {

    /**
     * @SLX\Route(
     *      @SLX\Request(method="GET", uri="/{id}")
     * )
     * @param Application $app
     * @param $name
     * @return
     */
    public function getById_GET(Application $app, $id){
        $requestData = array();
        $requestData['id'] = $id;
        $response = ProductController::curl_get_content('http://192.168.0.241/eanlist?type=Web',$requestData);
        $result = ProductController::building_result($response);

        return $app->render('products/product.detail.twig', $result);
    }

    /**
     * @SLX\Route(
     *      @SLX\Request(method="GET", uri="/search/{name}")
     * )
     * @param Application $app
     * @param $name
     * @return
     */
    public function getByName_GET(Application $app, $name){
        $requestData = array();
        $requestData['names'] = $name;
        $response = ProductController::curl_get_content('http://192.168.0.241/eanlist?type=Web',$requestData);
        $result = ProductController::building_result($response);

        return $app->render('products/products.twig', $result);
    }

    public static function curl_get_content($url, $requestData){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,  $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $requestData);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        $response = json_decode($response);
        curl_close($curl);
        return $response;
    }
    public static function building_result($response){
      $result = [];
      for ($i =0; $i < count($response) ;$i++) {
          $prod = array();
          $prod['ean'] = $response[$i]['barcode'];
          $prod["name"]= $response[$i]['itemName'];
          $prod["prices"] = ProductController::building_prices($response[$i]['prices'])
          $result[] = $prod;
      }
      return $result;
    }
    public static function building_prices($prices){
      $prod["prices"] = array();
      for ($j=0;$j < count($prices); $j++) {
          if ($prices[$j]['currencyCode'] != 'ZAR') {
              $p_price = array();
              $p_price['price'] = $prices[$j]['sellingPrice'];
              $p_price['currency'] = $prices[$j]['currencyCode'];
              $prod["prices"][] = $p_price;
          }
      }
      return $prod["prices"];
    }

}
