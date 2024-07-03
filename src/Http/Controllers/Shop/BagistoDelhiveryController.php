<?php

namespace CodeRomeos\BagistoDelhivery\Http\Controllers\Shop;

use CodeRomeos\BagistoDelhivery\Services\Delhivery;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Webkul\Product\Models\Product;
use Webkul\Checkout\Facades\Cart;

class BagistoDelhiveryController extends Controller
{
    use DispatchesJobs, ValidatesRequests;

    protected $delhiveryApi;

    public function __construct()
    {
        $delhiveryApi = new Delhivery;
    }

    public function tracking(Request $request)
    {
        $data = [
            'tracking_data' => null
        ];

        if ($request->filled('awbCode')) {
            $delhiveryApi = new Delhivery;
            $data = $delhiveryApi->trackAWB($request->awbCode);
            

            $data = [
            "ShipmentData" => [
                    [
                        "Shipment" => [
                        "AWB" => "83750410000022", 
                        "CODAmount" => 0, 
                        "ChargedWeight" => null, 
                        "Consignee" => [
                            "Address1" => [
                            ], 
                            "Address2" => [
                                ], 
                            "Address3" => "", 
                            "City" => "New Delhi", 
                            "Country" => "India", 
                            "Name" => "John Doe", 
                            "PinCode" => 110034, 
                            "State" => "", 
                            "Telephone1" => "", 
                            "Telephone2" => "" 
                        ], 
                        "DeliveryDate" => null, 
                        "DestRecieveDate" => null, 
                        "Destination" => "New Delhi", 
                        "DispatchCount" => 0, 
                        "Ewaybill" => [
                                    ], 
                        "ExpectedDeliveryDate" => null, 
                        "Extras" => "", 
                        "FirstAttemptDate" => null, 
                        "InvoiceAmount" => 0, 
                        "OrderType" => "Pre-paid", 
                        "Origin" => "Kirtinagar_PC (DELHI)", 
                        "OriginRecieveDate" => null, 
                        "OutDestinationDate" => null, 
                        "PickUpDate" => "2024-07-03T11:05:29.366", 
                        "PickedupDate" => null, 
                        "PickupLocation" => "Delhi", 
                        "PromisedDeliveryDate" => null, 
                        "Quantity" => "", 
                        "RTOStartedDate" => null, 
                        "ReferenceNo" => "12345t", 
                        "ReturnPromisedDeliveryDate" => null, 
                        "ReturnedDate" => null, 
                        "ReverseInTransit" => false, 
                        "Scans" => [
                                        [
                                            "ScanDetail" => [
                                                "Instructions" => "Shipment details manifested", 
                                                "Scan" => "Manifested", 
                                                "ScanDateTime" => "2024-07-03T11:05:29.663", 
                                                "ScanType" => "UD", 
                                                "ScannedLocation" => "Kirtinagar_PC (DELHI)", 
                                                "StatusCode" => "X-UCI", 
                                                "StatusDateTime" => "2024-07-03T11:05:29.663" 
                                            ] 
                                        ] 
                                    ], 
                        "SenderName" => "bde6d9-TheNutritionHut-do-cdp", 
                        "Status" => [
                                                "Instructions" => "Shipment details manifested", 
                                                "RecievedBy" => "", 
                                                "Status" => "Manifested", 
                                                "StatusCode" => "X-UCI", 
                                                "StatusDateTime" => "2024-07-03T11:05:29.663", 
                                                "StatusLocation" => "Kirtinagar_PC (DELHI)", 
                                                "StatusType" => "UD" 
                                                ] 
                        ] 
                    ] 
                ] 
            ]; 
 
 
        }
        if(isset($data['ShipmentData']) && count($data['ShipmentData']) > 0) {
            $data = $data['ShipmentData'][0]['Shipment'];
        }

        return view('bagistodelhivery::shop.tracking', ['tracking_data' => $data, 'awbCode' => $request->awbCode]);
    }

    public function getEstimatedDelivery(Request $request)
    {
        $request->validate([
            'pickup_postcode' => 'sometimes|required|integer|digits:6',
            'delivery_postcode' =>  'required|integer|digits:6',
            'product_id' => 'sometimes|required|integer|exists:products,id',
            'weight' => 'sometimes|required|numeric',
            'cod' => 'sometimes|required|boolean'
        ]);
        $product = Product::find($request->product_id);
        $weight =
            $product
            ?->attribute_values()
            ?->whereHas('attribute', function ($attributes) {
                $attributes->where('code', 'weight');
            })
            ->first()?->text_value ?? 0.5;

        $pickUpAddress = app('Webkul\Inventory\Repositories\InventorySourceRepository')->getModel()->latest()->first();

        $request->mergeIfMissing([
            'pickup_postcode' => config('delhivery.pickupPostcode') ?? $pickUpAddress->postcode,
            'weight' => $weight,
            'cod' => 0
        ]);

        $delhiveryApi = new Delhivery;
        $data = $delhiveryApi->getEstimatedDelivery($request);
        return response()->json($data);
    }

    public function checkPincodeAvailability(Request $request)
    {
        $request->validate([
            'pickup_postcode' => 'sometimes|required|integer|digits:6',
            'delivery_postcode' =>  'required|integer|digits:6',
            'weight' => 'sometimes|required|numeric',
            'cod' => 'sometimes|required|boolean'
        ]);

        $cart = Cart::getCart();
        $pickUpAddress = app('Webkul\Inventory\Repositories\InventorySourceRepository')->getModel()->latest()->first();

        $calculateTotalWeight = 0;

        foreach ($cart->items as $item) {
            if ($item->getTypeInstance()->isStockable()) {
                $calculateTotalWeight += $item->total_weight;
            }
        }

        $request->mergeIfMissing([
            'pickup_postcode' => config('delhivery.pickupPostcode') ?? $pickUpAddress->postcode,
            'weight' => $request->weight ?? $calculateTotalWeight ?? 0.5,
            'cod' => 0
        ]);

        $delhiveryApi = new Delhivery;
        $data = $delhiveryApi->getEstimatedDelivery($request);
        return response()->json($data);
    }
}
