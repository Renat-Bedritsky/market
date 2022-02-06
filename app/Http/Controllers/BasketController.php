<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;

class BasketController extends Controller
{
    protected function getBasket(Request $request)
    {
        $user = new User;
        $userData = $this->checkCookieLogin();
        if (empty($userData['author_id'])) {
            return redirect('auth');
        }
        $info['userData'] = $userData;

        $jsonBasket = $user->getBasket($userData['author_id']);
        $basket = (array)json_decode($jsonBasket[0]['basket']);

        if (isset($request['plus']) && !empty($userData)) {
            $this->processingPlusBasket($basket, $request['plus'], $userData['author_id']);
            return redirect('basket');
        }
        if (isset($request['minus']) && !empty($userData)) {
            $this->processingMinusBasket($basket, $request['minus'], $userData['author_id']);
            return redirect('basket');
        }
        $info['products'] = $this->addInfoProduct($basket);
        $basketPrice = $this->basketPrice($basket);
        $info += ['basket_price' => $basketPrice];

        return view('basket', ['info' => $info]);
    }

    private function addInfoProduct($basket)
    {
        $products = new Product;
        $productsInformation = [];
        foreach ($basket as $product => $count) {
            $infoProduct = $products->infoProduct($product);
            if (!sizeof($infoProduct)) {
                $this->removeRemovedProduct($basket, $product);
            }
            $infoProduct[0]['count'] = $count;
            array_push($productsInformation, $infoProduct[0]);
        }
        return $productsInformation;
    }

    private function basketPrice($basket)
    {
        $products = new Product;
        $grantTotal = 0;
        foreach ($basket as $product => $count) {
            $infoProduct = $products->infoProduct($product);
            $grantTotal += $infoProduct[0]['price'] * $count;
        }
        return $grantTotal; 
    }

    private function removeRemovedProduct($basket, $product)
    {
        $user = new User;
        $userData = $user->checkCookieLogin(); 
        unset($basket[$product]);
        $jsonBasket = json_encode($basket);
        $user->updateBasket($jsonBasket, $userData['author_id']);
        header('Refresh: 0');   // TO DO
    }

    protected function clearBasket()
    {
        $user = new User;
        $userData = $this->checkCookieLogin();
        $user->clearBasket($userData['author_id']);
        return redirect('basket');
    }
}
