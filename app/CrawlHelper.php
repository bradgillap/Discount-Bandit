<?php

use Illuminate\Support\Str;
use \Illuminate\Support\Facades\Log;

function get_numbers_only_with_dot($sentence)
{
    return preg_replace('/[^0-9.]/', '', $sentence);
}
function get_product_name($center_column)
{
    try {
         return trim($center_column->xpath("//span[@id='productTitle'][1]")[0]->__toString());
        }
        catch (Throwable |Exception $e)
        {
            Log::debug("Couldn't get the name of the product");
            Log::error($e);
            return "NA";
        }
}


function get_shipping_price($right_column)
{
    try {
        $shipping_price=$right_column->xpath("//div[@id='deliveryBlockMessage']//span[@data-csa-c-delivery-price]")[0]->__toString();
        return (float) get_numbers_only_with_dot($shipping_price);
    }
    catch (Throwable | Exception $e)
    {
        Log::debug("Something Wrong Happened while getting the shipping price");
        Log::error($e);
    }



    return 0;
}


function get_original_price($center_column, $currency)
{

    //method 1 to return the price of the product
    try {
        return  100 * (float) Str::replace($currency , "" ,$center_column->xpath("(//span[contains(@class, 'apexPriceToPay')])[1]")[0]->span->__toString());
    }
    catch ( Exception | Throwable  $e )
    {
        Log::debug("Getting price with the first method didn't work");
        Log::error($e);
    }

    //method 2 to return the price of the product
    try {
        return  100 * (float) Str::replace($currency , "" ,$center_column->xpath("(//div[@id='apex_desktop']//div[@id='corePriceDisplay_desktop_feature_div']//span[@class='a-offscreen'])[1]")[0]->__toString());
    }
    catch (Throwable | Exception $e )
    {
        Log::debug("Getting price with the second method didn't work");
        Log::error($e);
    }

}


function get_seller($right_column)
{
    try {
        return $right_column->xpath("//div[@id='tabular_feature_div']//div[@class='tabular-buybox-text'][last()]//span")[0]->__toString();
    }
    catch (Throwable | Exception $e )
    {
        Log::debug("Couldn't get the seller for this product");
        Log::error($e);
        return "NA";
    }
}

function get_number_of_rates($center_column)
{
    try {
        $ratings=$center_column->xpath("//span[@id='acrCustomerReviewText']")[0]->__toString();
        return (int) get_numbers_only_with_dot($ratings);
    }
    catch (Throwable |  Exception $e)
    {
        Log::debug("Couldn't get the number of ratings");
        Log::error($e);
        \Illuminate\Support\Facades\Log::error("Error getting the rate number " . $e);
    }
    return 0;
}

function get_rate_star($xpath)
{
    try {
        return (float) explode(' out' , $xpath->query("//div[@id='averageCustomerReviews']//span[@id='acrPopover']//span[@class='a-icon-alt']")->item(0)->nodeValue)[0];
    }
    catch (Throwable | Exception $e )
    {
        \Illuminate\Support\Facades\Log::error("Couldn't get the star rating" . $e);
        return -1;
    }
}

function get_coupons($doc)
{
    $coupons_nodes= $doc->getElementById('reinvent_price_desktop_qualifiedBuybox')->getElementsByTagName('label');
    $count_coupons_nodes=$coupons_nodes->length;
    $coupons_available=[];
    for ($i=0 ; $i <$count_coupons_nodes; $i++) {
        $coupon_string=Str::before($coupons_nodes->item($i)->textContent, "%");
        if (Str::length($coupon_string)>=2)
            $coupons_available[]=substr($coupon_string , -2) . "%";
    }
}

function check_is_top_deal($center_column)
{
    try {
        $top_deal=$center_column->xpath("//div[@id='apex_desktop']//div[@class='offersConsistencyEnabled']//div[@id='dealBadge_feature_div']//span//span//span")[0]->__toString();
        if ($top_deal)
            return true;
    }
    catch (Throwable | Exception $e)
    {
        Log::debug("Couldn't Check if it's a top deal");
        Log::error($e);
    }
    return  false;
}


