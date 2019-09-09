<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\SaleBody;
use App\Models\SaleHeader;
use Illuminate\Http\Request;
use Dnetix\Redirection\PlacetoPay;
use Psy\Util\Str;

class CartController extends Controller
{
    public function submitOrder(Request $request)
    {

        $this->validate($request, [
            'name' => 'required',
            'last_name' => 'required',
            'address' => 'required',
        ]);

        $placetopay = new PlacetoPay([
            'login' => config('placetopay.login'),
            'tranKey' => config('placetopay.tran_key'),
            'url' => config('placetopay.ws_url'),
        ]);

        $name = $request->name;

        $order = $this->saveOrder($request);

        $request = [
            'payment' => [
                'reference' => $order->number,
                'description' => $name . 'Orden Nro'. $order->number,
                'amount' => [
                    'currency' => 'USD',
                    'total' => 120,
                ],
            ],
            'expiration' => date('c', strtotime('+2 days')),
            'returnUrl' =>  config('app.url') .  '/' . config('app.path') . '/order/' .$order->number,
            'ipAddress' => '127.0.0.1',
            'userAgent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36',
        ];

        $response = $placetopay->request($request);
        if ($response->isSuccessful()) {
            // STORE THE $response->requestId() and $response->processUrl() on your DB associated with the payment order
            // Redirect the client to the processUrl or display it on the JS extension
//            header('Location: ' . $response->processUrl());
//            return redirect($response->processUrl());
            return response()->json([
                'error'=> false, 'msg' => 'Redirigiendo...',
                'redirect_url' => $response->processUrl()
            ]);
        } else {
            // There was some error so check the message and log it

            return response()->json(['error'=> true, 'msg' => 'Error procesando orden']);
        }

    }

    protected function saveOrder($request)
    {
        $new_header = SaleHeader::create([
            'number' => \Illuminate\Support\Str::random(10),
            'client' => $request->name . $request->last_name
        ]);

        $category = ProductCategory::firstOrCreate([
            'title' => 'CATEGORIA PRINCIPAL',
            'tag' => 'main'
        ]);

        $prod = Product::firstOrCreate([
            'name' => 'PELOTA',
            'sku' => \Illuminate\Support\Str::random(4),
            'product_category_id' => $category->id
        ]);

        foreach ($request->products as $product) {
            if((int)$product['quantity'] > 0) {
                $new_body = SaleBody::create([
                    'sale_header_id' => $new_header->id,
                    'product_id' => $prod->id,
                    'quantity' => (int)$product['quantity']
                ]);
            }
        }
        return $new_header;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Dnetix\Redirection\Exceptions\PlacetoPayException
     */
    public function getOrders()
    {
        $orders = SaleHeader::all();

        return response()->json($orders);
    }

    /**
     * @param $order_number
     * @return \Illuminate\Http\JsonResponse
     * @throws \Dnetix\Redirection\Exceptions\PlacetoPayException
     */
    public function getOrderStatus($order_number)
    {
        $order = SaleHeader::where('number', $order_number)->first();

        if(is_null($order)) {
            return response()->json([
                'error' => true,
                'msg' => 'Orden no encontrada'
            ]);
        }

        $placetopay = new PlacetoPay([
            'login' => config('placetopay.login'),
            'tranKey' => config('placetopay.tran_key'),
            'url' => config('placetopay.ws_url'),
        ]);

        $response = $placetopay->query($order->number);

        if ($response->isSuccessful()) {
            // In order to use the functions please refer to the Dnetix\Redirection\Message\RedirectInformation class

            if ($response->status()->isApproved()) {
                $order->state = 'approved';
            }
        } else {
            // There was some error with the connection so check the message
//                print_r($response->status()->message() . "\n");
        }

        $data = $response;

        $order->save();

        return response()->json([
            'order' => $order,
            'data' => $data,
            'error' => false
        ]);
    }
}
