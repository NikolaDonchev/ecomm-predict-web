<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RecommendationsController extends Controller {

    public function recommend(Request $request) {

        // Get customer ID from the request
        $customer_id = $request->get('customer_id');
        // For information
        $recommendationType = null;

        // If an ID is passed get similarity recommendations
        if($customer_id) {
            $recommendations = \App\Similarity::where('customer_id', $customer_id)->get()->toArray();
            $recommendationType = 'Similarity';
            // If there are not recommendations for this customer proceed with Popularity
            if (count($recommendations) === 0) {
                $recommendations = \App\Popularity::where('customer_id', 1)->get()->toArray();
                $recommendationType = 'Popularity because customer does not exist';
            }
        }
        // If no ID is passed get popularity recommendations
        else {
            $recommendations = \App\Popularity::where('customer_id', 1)->get()->toArray();
            $recommendationType = 'Popularity';
        }

        // Get one recommendation on random from the top 3 ones
        $recommendedProductId = array_rand($recommendations, 1);
        
        // Get the product information for this recommendation
        $recommendedProduct = \App\Product::where('id', $recommendations[$recommendedProductId]['product_id'])->first();

        // Return a JSON object with the product information
        return \Response::json([
            'type' => $recommendationType,
            'title' => $recommendedProduct['title'],
            'description' => $recommendedProduct['description'],
            'image_url' => $recommendedProduct['image_url'],
            'product_url' => $recommendedProduct['product_url'],
            'button' => $recommendedProduct['button']
        ]);
    }

}
