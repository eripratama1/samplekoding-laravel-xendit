<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
        return view('order', [
            'order' => Order::latest()->get()
        ]);
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
            return to_route('home');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function notificationCallback(Request $request)
    {
        /**
         * Mendapatkan token xendit dari webhooks
         */
        $getToken = $request->headers->get('x-callback-token');

        /**
         * Mengabil nilai token yang ditambahakan pada file .env
         */
        $callbackToken = env('XENDIT_CALLBACK_TOKEN');

        try {
            $order = Order::where('external_id', $request->external_id)->first();

            /**
             * Kondisi jika nilai callback token kosong pada file .env
             */
            if (!$callbackToken) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Callback token xendit not exists'
                ], Response::HTTP_NOT_FOUND);
            }

            /**
             * Kondisi jika nilai token yang didapatkan tidak sama dengan
             * nilai token yang dimuat pada file .env
             */
            if ($getToken !== $callbackToken) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Token callback invalid'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            /**
             * Jika responses status bernilai PAID
             * ubah status pembayaran pada tabel orders
             */
            if ($order) {
                if ($request->status === 'PAID') {
                    $order->update([
                        'status' => 'Completed'
                    ]);
                } else {
                    $order->update([
                        'status' => 'Failed'
                    ]);
                }
            }

            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'callback sent'
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
