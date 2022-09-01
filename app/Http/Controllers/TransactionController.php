<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Transaction;
use Validator;
class TransactionController extends Controller
{
    //
    public function index()
    {
        $transaction = Transaction::where('user_id', Auth::user()->id)->get();

        $detail_transaction = array();
        $income = 0;
        $spending = 0;
        foreach ($transaction as $key => $item) {
           
            array_push($detail_transaction, [
                "title" => $item->title,
                "value" => "Rp " . number_format($item->value,2,',','.'),
                "type" => $item->meta,                
                "date" => date_format($item->created_at , 'd, M Y')

            ]);
            if ($item->meta == 'income') {
                $income += $item->value;
            }
            if ($item->meta == 'spending') {
                $spending += $item->value;
            }  
        }
        $payload = [
            'name_user' => Auth::user()->name,
            'source' => 'total transaksi',
            'detail_transaction' => $detail_transaction,
            'total_income' => "Rp " . number_format($income,2,',','.'),
            'total_spending' => "Rp " . number_format($spending,2,',','.'),
            'jumlah uang tersisa anda' => "Rp " . number_format(($income - $spending),2,',','.'),

        ];
        $response = [
            'success' => true,
            'error' => [],
            'message' => 'Semua Transaksi',
            'data' => $payload
        ];
        return response()->json($response, 200);
    }

    public function store(Request $request)
    {
    try {
        $validator = Validator::make($request->all(),[
            'title' => 'required|string',
            'value' => 'required|integer',
            'notes' => 'required|string',
            'meta' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());
        }

        $transaction = new Transaction();
        $transaction->title = $request->title;
        $transaction->value = $request->value;
        $transaction->notes = $request->notes;
        $transaction->meta = $request->meta;
        $transaction->user_id = Auth::user()->id;
        $transaction->save();

        $response = [
            'success' => true,
            'error' => [],
            'message' => 'Transaksi berhasil Ditambah',
            'data' => $transaction
        ];
        return response()->json($response, 200);
    }
    catch (\Throwable $th) {
        //throw $th;
        $response = [
            'success' => false,
            'error' => [],
            'message' => 'Transaksi gagal ditambahkan',
            'data' => null
        ];
        
        return response()->json($response, 400);
        }
    }

    public function spending()
    {
        $transaction = Transaction::where([['meta', 'spending'], ['user_id', Auth::user()->id]])->get();

        $detail_transaction = array();
        $total = 0;
        foreach ($transaction as $key => $item) {
            $total += $item->value;
            array_push($detail_transaction, [
                "title" => $item->title,
                "value" => "Rp " . number_format($item->value,2,',','.'),
                "date" => date_format($item->created_at , 'd, M Y')

            ]);
        }
        $payload = [
            'name_user' => Auth::user()->name,
            'source' => 'spending',
            'detail_transaction' => $detail_transaction,
            'total' => "Rp " . number_format($total,2,',','.'),

        ];

        $response = [
            'success' => true,
            'error' => [],
            'message' => 'Data Spending',
            'data' => $payload
        ];
        return response()->json($response, 200);
    }

    public function income()
    {
        $transaction = Transaction::where([['meta','income'],['user_id', Auth::user()->id]])->get();
        $detail_transaction = array();
        $total = 0;
        foreach ($transaction as $key => $item) {
            $total += $item->value;
            array_push($detail_transaction, [
                "title" => $item->title,
                "value" => "Rp " . number_format($item->value,2,',','.'),
                "date" => date_format($item->created_at , 'd, M Y')
            ]);
        }

        $payload = [
            'name_user' => Auth::user()->name,
            'source' => 'Income',
            'detail_transaction' => $detail_transaction,
            'total' => "Rp " . number_format($total,2,',','.')
        ];

        $response = [
            'success' => true,
            'error' => [],
            'message' => 'Data Income',
            'data' => $payload
        ];
        return response()->json($response, 200);
    }

}
