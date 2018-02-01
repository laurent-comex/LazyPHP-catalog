<?php

namespace Catalog\models;

use Core\Model;
use Core\Session;

class CartItem
{
    /**
     * @var \Catalog\models\Product
     */
    public $product = null;

    /**
     * @var int
     */
    public $quantity = 0;

    public function __construct($product, $quantity = 1)
    {
        $this->product = $product;
        $this->quantity = $quantity;
    }

    public function getTotal()
    {
        $price = number_format($this->product->getPrice(), 2);

        return round($price* $this->quantity,2);
    }
}

class Cart
{
    /**
     * @var CartItem[]
     */
    public $items = array();

    public static function load()
    {
        $cart = Session::get('cart');
        if ($cart === null) {
            $cartClass = Model::loadModel('Cart');
            $cart = new $cartClass();
        }
        return $cart;
    }

    public function save()
    {
        Session::set('cart', $this);
    }

    /**
     * Add an item (product + quantity) to the cart
     * @param \Catalog\models\Product $product
     * @param int $quantity
     */
    public function addItem($product, $quantity = 1)
    {
        $itemIndex = false;
        foreach ($this->items as $index => $item) {
            if ($item->product->id == $product->id) {
                $itemIndex = $index;
                break;
            }
        }

        if ($itemIndex === false) {
            $cartItemClass = Model::loadModel('CartItem');
            $this->items[] = new $cartItemClass($product, $quantity);
        } else {
            $this->items[$itemIndex]->quantity = $this->items[$itemIndex]->quantity + $quantity;
        }
    }

    /**
     * Delete an item from the cart
     * @param \Catalog\models\Product $product
     */
    public function deleteItem($product)
    {
        foreach ($this->items as $index => $item) {
            if ($item->product->id == $product->id) {
                array_splice($this->items, $index, 1);
                break;
            }
        }
    }

    /**
     * Empty the cart
     */
    public function clean()
    {
        $this->items = array();
    }

    /**
     * Set quantity of an item
     * @param \Catalog\models\Product $product
     * @param int $quantity
     */
    public function setItemQuantity($product, $quantity)
    {
        $itemIndex = false;
        foreach ($this->items as $index => $item) {
            if ($item->product->id == $product->id) {
                $itemIndex = $index;
                break;
            }
        }

        if ($itemIndex === false) {
            $this->addItem($product, $quantity);
        } else {
            if ($quantity > 0) {
                $this->items[$itemIndex]->quantity = $quantity;
            } else {
                $this->deleteItem($product);
            }
        }
    }

    /**
     * Get the total price of the cart
     * @return float
     */
    public function getTotal()
    {
        $total = 0.0;

        foreach ($this->items as $index => $item) {
            $total = $total + $item->getTotal();
        }

        return $total;
    }

    public function createOrder($params = array())
    {
        $site = Session::get('site');
        if ($site == null) {
            $site = isset($params['site']) ? $params['site'] : null;
        }

        $current_user = Session::get('current_user');
        if ($current_user == null) {
            $current_user = isset($params['current_user']) ? $params['current_user'] : null;
        }

        if ($site === null || $current_user === null) {
            throw new \Exception('Cannot create order : user unknown');
        }

        $orderClass = Model::loadModel('Order');
        $order = new $orderClass();
        
        $order->site_id = $site !== null ? $site->id : null;
        $order->user_id = $current_user !== null ? $current_user->id : null;

        $order->save();

        $orderDetailClass = Model::loadModel('OrderDetail');
        foreach ($this->items as $item) {
            $orderDetail = new $orderDetailClass();
            $orderDetail->order_id = $order->id;
            $orderDetail->product_id = $item->product->id;
            $orderDetail->quantity = $item->quantity;
            $orderDetail->amount = $item->getTotal();
            $orderDetail->save();
        }

        return $order;
    }
}
