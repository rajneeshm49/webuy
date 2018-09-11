<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
/**
 * Shops Controller
 **/
class ShopsController extends AppController
{
	public function initialize()
    {
        parent::initialize();
    }
	public function dashboard()
	{
		$this->loadModel('Products');

		$products = $this->Products->find('all');
		//$this->set(compact('products', 'cart_count'));
		$this->set([
			'success' => true,
            'products' => $products,
            '_serialize' => ['products', 'success']
        ]);
	}

	public function addtocart()
	{
		$params = $this->request->getQuery();
		if(empty($params['id'])) {
			$this->set([
				'success' => false,
				'message' => 'Please choose product to add to cart',
				'_serialize' => ['success', 'message']
			]);
		}
		$product_id = $params['id'];
		$user_id = $this->Auth->user('id');
		$this->loadModel('Cart');
		$cart = $this->Cart->newEntity();

		$cart->user_id = $user_id;
		$cart->product_id = $product_id;

		if ($this->Cart->save($cart)) {
		    $this->set([
				'success' => true,
				'message' => 'product successfully added to cart',
				'_serialize' => ['success', 'message']
			]);
		}
	}

	public function delfromcart()
    {
		// $params = $this->request->getQuery();
		$params = $this->request->getData();
		if(empty($params['id'])) {
			$this->set([
				'success' => false,
				'message' => 'No product found to be deleted from cart',
				'_serialize' => ['success', 'message']
			]);
		}
        $this->loadModel('Cart');
        $cart_product = $this->Cart->get($params['id']);
        if ($this->Cart->delete($cart_product)) {
			$this->set([
				'success' => true,
				'message' => 'The product has been deleted from your cart.',
				'_serialize' => ['success', 'message']
			]);
        } else {
			$this->set([
				'success' => true,
				'message' => 'The product could not be deleted from your cart. Please, try again.',
				'_serialize' => ['success', 'message']
			]);
        }
    }

	public function cartlist()
	{
		$this->loadModel('Cart');
		$cart_products = $this->Cart->find('all')->where(['user_id' => $this->Auth->user('id')])->contain(['Products','Users']);
		//print_r($cart_products);exit;
		$cart_items = array();
		$cnt = 1;
		foreach($cart_products as $cart_product) {
			$cart_items[] = array(
				'position' => $cnt,
				'product_name' => $cart_product['product']->title,
				'price' => $cart_product['product']->price,
				'image' => $cart_product['product']->image,
				'id' => $cart_product['id']
			);
			$cnt++;
		}
		$this->set([
			'success' => true,
			'cart_products' => $cart_items,
			'_serialize' => ['success', 'cart_products']
		]);
	}

	public function checkout()
	{
		$this->loadModel('Cart');
		$this->loadModel('Orders');
		$cart_products = $this->Cart->find('all')->where(['user_id' => $this->Auth->user('id')]);
		foreach($cart_products as $cart_product) {
			$order = $this->Orders->newEntity();
			$order->product_id = $cart_product->product_id;
			$order->user_id = $cart_product->user_id;
			$order->date = date('Y-m-d H:i:s');
			$this->Orders->save($order);
			$this->Cart->delete($cart_product);
		}

		$this->set([
			'success' => true,
			'message' => 'Thank you for placing your order',
			'_serialize' => ['success', 'message']
		]);
	}

	public function cartitemcount() {
		$this->loadModel('Cart');
		$cart_count = $this->Cart->find('all')->where(['user_id'=> $this->Auth->user('id')])->count();
		$this->set([
			'success' => true,
			'count' => $cart_count,
			'_serialize' => ['success', 'count']
		]);
	}
	
    public function thankyou()
    {

    }

}
