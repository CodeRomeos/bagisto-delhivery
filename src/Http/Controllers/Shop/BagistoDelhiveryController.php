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
        }

        dd($data);

        return view('bagistodelhivery::shop.tracking', ['tracking_data' => $data['tracking_data'], 'awbCode' => $request->awbCode]);
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
