<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use League\Csv\Reader;

use App\Customer;
use App\LineItem;

class ImportData extends Controller {
    
    public function index() {
        
        // Load the CSV file
        $csv = Reader::createFromPath('../storage/app/orders_export_2.csv');
        // Set the header row
        $csv->setHeaderOffset(0);

        // Get header row columns
        $header = $csv->getHeader();
        // Get the rest of the records
        $records = $csv->getRecords();

        // Map the Product SKU to its ID
        $map_products = [
            // Powder Normal
            'H-ORIG-VA-23' => '1466095894627',
            'H-ORIG-VN-23' => '1466096189539',
            'H-ORIG-CO-23' => '1466095861859',
            'H-ORIG-BE-23' => '1466095829091',
            'H-ORIG-UU-23' => '1466095927395',
            'H-ORIG-MC-23' => '1466095960163',
            // Powder Pro
            'H-PRO-VN-23' => '1466318520419',
            // RTD
            'H-RTD12-VA-10' => '1466096386147',
            'H-RTD12-BE-10' => '1466096353379',
            // Bars
            'BAR16-CF-30' => '1466096287843',
            'BAR16-OG-30' => '1466096255075',
            'BAR16-CO-30' => '1466096320611',
            // Granola
            'H-GN-OR-11' => '1466096418915',
            'H-GN-BE-11' => '1466096451683',
            // Flavour Boost
            'HF-CHOCMINT' => '1239867981830',
            'HF-CARAMEL' => '844580651014',
            'HF-STRAW' => '11525025414',
            'HF-MOCHA' => '9672405955',
            'HF-BANANA' => '15571182086',
            'HF-COCONUT' => '15486872454',
            'HF-CHOCOLATE' => '15571187078',
            'HF-CACAO' => '33141363974',
            'HF-MATCHA' => '33141385926',
            'HF-RHUBARD' => '9672385859',
            'HF-TOFFEE' => '9672299203'
        ];

        // Iterate through all of the records
        foreach($records as $record) {

            // First check if the customer already exists
            $customer = Customer::where('email', '=', $record['Email'])->first();
            if ($customer === NULL) {
                // Save the new customer
                $customer = new Customer;
                $customer->email = $record['Email'];
                $customer->save();
            }

            // If the product is part of the map
            // we add it to the line_items table
            if (array_key_exists($record['Lineitem_sku'], $map_products)) {
                // Add the order information for this customer
                $lineItem = new LineItem;
                $lineItem->product_id = $map_products[$record['Lineitem_sku']];
                $lineItem->customer_id = $customer->id;
                $lineItem->quantity = $record['Lineitem_quantity'];
                $lineItem->save();
            }

        }

        echo "Done!";

    }
}
