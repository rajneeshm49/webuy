<?php
namespace App\Controller\Admin;

use App\Controller\AppController;

class OrdersController extends AppController
{
	public function index()
    {
        $orders = $this->Orders->find('all')->contain(['Users', 'Products']);
        
        $cnt = 1;
        foreach($orders as $order) {
			$orders_arr[] = array(
				'position' => $cnt,
				'product_name' => $order->product['title'],
                'user_name' => $order->user['name'],
                'date' => $order->date,
				'id' => $order->id
			);
			$cnt++;
        }

        $this->set([
            'orders' => $orders_arr,
            'success' => true,
            '_serialize' => ['orders', 'success']
        ]);
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $id = $this->request->getData('id');
        $order = $this->Orders->get($id);
        if ($this->Orders->delete($order)) {
            $this->set([
                'message' => 'Order successfully deleted',
                'success' => true,
                '_serialize' => ['message', 'success']
            ]);
        } else {
            $this->set([
                'message' => 'Error while deleting order',
                'success' => false,
                '_serialize' => ['message', 'success']
            ]);
        }
    }
}
