<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    function addOrder($userId, $name, $phone, $email, $products, $price)
    {
        Order::insert([
            'user_id' => $userId,
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'operator' => '',
            'products' => $products,
            'price' => $price,
            'status' => 'get',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);
    }

    function getNewOrders()
    {
        return Order::select('id', 'name', 'phone', 'email', 'products', 'price', 'status', 'created_at')->where('status', '=', 'get')->get();
    }

    function getDoneOrders($operator)
    {
        return Order::select('id', 'name', 'phone', 'email', 'products', 'price', 'status', 'updated_at')->where('operator', '=', $operator)->get();
    }

    function doneOrder($operator, $id)
    {
        Order::where('id', $id)->update(['operator' => $operator], ['updated_at' => date("Y-m-d H:i:s")]);
        Order::where('id', $id)->update(['status' => 'done']);
    }

    function canceledOrder($operator, $id)
    {
        Order::where('id', $id)->update(['operator' => $operator], ['updated_at' => date("Y-m-d H:i:s")]);
        Order::where('id', $id)->update(['status' => 'canceled']);
    }
}
