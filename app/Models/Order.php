<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    function addOrder($user_id, $name, $phone, $email, $products, $price) {
        date_default_timezone_set('Europe/Minsk');
        
        Order::insert([
            'user_id' => $user_id,
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

    function getNewOrders() {
        return Order::select('id', 'name', 'phone', 'email', 'products', 'price', 'status', 'created_at')->where('status', '=', 'get')->get();
    }

    function getDoneOrders($operator_name) {
        return Order::select('id', 'name', 'phone', 'email', 'products', 'price', 'status', 'updated_at')->where('operator', '=', $operator_name)->get();
    }

    function doneOrder($operator, $id) {
        date_default_timezone_set('Europe/Minsk');
        $updated_at = date("Y-m-d H:i:s");

        Order::where('id', $id)->update(
            ['operator' => $operator],
            ['updated_at' => $updated_at]
        );

        Order::where('id', $id)->update(
            ['status' => 'done']
        );
    }

    function canceledOrder($operator, $id) {
        date_default_timezone_set('Europe/Minsk');
        $updated_at = date("Y-m-d H:i:s");
        
        Order::where('id', $id)->update(
            ['operator' => $operator],
            ['updated_at' => $updated_at]
        );

        Order::where('id', $id)->update(
            ['status' => 'canceled']
        );
    }
}
