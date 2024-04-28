<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Xendit\Configuration;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\InvoiceItem;

class OrderController extends Controller
{
    public function __construct()
    {
        /**
         * Menambahkan configuration api key xendit
         */
        Configuration::setXenditKey(env('XENDIT_SECRET_KEY'));
    }

    public function index()
    {
        return view('order');
    }

    public function createInvoice(Request $request)
    {
        try {

            $no_transaction = 'Inv - ' . rand();
            $order = new Order;
            $order->no_transaction = $no_transaction;
            $order->external_id = $no_transaction;
            $order->item_name = $request->input('item_name');
            $order->qty = $request->input('qty');
            $order->price = $request->input('price');
            $order->grand_total = $request->input('grand_total');

            /**
             * Membuat properti items yang akan ditambahkan
             * saat membuat invoice
             */
            $items = new InvoiceItem([
                'name' => $request->input('item_name'),
                'price' => $request->input('price'),
                'quantity' => $request->input('qty')
            ]);

            /**
             * Membuat invoice dengan method CreateInvoiceReqeust()
             */
            $createInvoice = new CreateInvoiceRequest([
                'external_id' => $no_transaction,
                'amount' => $request->input('grand_total'),
                'invoice_duration' => 172800,
                'items' => array($items)
            ]);

            /**
             * Membuat instance untuk pembuatan invoice
             */
            $apiInstance = new InvoiceApi();
            $generateInvoice = $apiInstance->createInvoice($createInvoice);

            /**
             * Simpan data invoice_url yang didapatkan dari response json
             * saat pembuatan invoice
             */
            $order->invoice_url = $generateInvoice['invoice_url'];
            $order->save();
            return dd($order);

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
