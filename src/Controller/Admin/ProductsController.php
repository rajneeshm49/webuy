<?php
namespace App\Controller\Admin;

use App\Controller\AppController;

/**
 * Products Controller
 *
 * @property \App\Model\Table\ProductsTable $Products
 *
 * @method \App\Model\Entity\Product[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProductsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $ctg_arr =array();
        $this->loadModel('Categories');
        $categories = $this->Categories->find('list', [
                                            'keyField' => 'id',
                                            'valueField' => 'category_name'
                                          ]);
        foreach ($categories as $key => $value) {
           $ctg_arr[$key] = $value;
        }
        $products = $this->Products->find('all');

        $products_arr = array();
		$cnt = 1;
		foreach($products as $product) {
			$products_arr[] = array(
				'position' => $cnt,
				'product_name' => $product->title,
                'price' => $product->price,
                'category' => $ctg_arr[$product->category],
				'image' => $product->image,
				'id' => $product->id
			);
			$cnt++;
        }
        
        $this->set([
            'products' => $products_arr,
            'success' => true,
            '_serialize' => ['products', 'success']
            ]);
    }

    /**
     * View method
     *
     * @param string|null $id Product id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->viewBuilder()->setLayout('admin');
        $product = $this->Products->get($id, [
            'contain' => []
        ]);

        $this->set('product', $product);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        // $this->loadModel('Categories');
        // $categories = $this->Categories->find('list', [
        //                                         'keyField' => 'id',
        //                                         'valueField' => 'category_name'
        //                                     ]);
        $product = $this->Products->newEntity();
        if (!empty($this->request->data)) {
            $imageFileName = '';
            
        if (!empty($this->request->data['image']['name'])) {
        $file = $this->request->data['image']; //put the data into a var for easy use

        $ext = substr(strtolower(strrchr($file['name'], '.')), 1); //get the extension
        $arr_ext = array('jpg', 'jpeg', 'gif', 'png'); //set allowed extensions
        $setNewFileName = time() . "_" . rand(000000, 999999);

        //only process if the extension is valid
        if (in_array($ext, $arr_ext)) {
            //do the actual uploading of the file. First arg is the tmp name, second arg is 
            //where we are putting it
            move_uploaded_file($file['tmp_name'], WWW_ROOT . '/img/products/' . $setNewFileName . '.' . $ext);

            //prepare the filename for database entry 
            $imageFileName = $setNewFileName . '.' . $ext;
            } else {
                echo 'dddd';exit;
            }
        }

        $product = $this->Products->patchEntity($product, $this->request->getData());

        $product->image = $imageFileName;
        
        if ($this->Products->save($product)) {
            $this->set([
                'message' => 'Product successfully added.',
                'success' => true,
                '_serialize' => ['message', 'success']
                ]);
           } else {
            $this->set([
                'message' => 'Error. Product could not be added.',
                'success' => false,
                '_serialize' => ['message', 'success']
                ]);
           }
        } else {echo 'iam here222';exit;}
    }

    /**
     * Edit method
     *
     * @param string|null $id Product id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->viewBuilder()->setLayout('admin');
        $product = $this->Products->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $product = $this->Products->patchEntity($product, $this->request->getData());
            if ($this->Products->save($product)) {
                $this->Flash->success(__('The product has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The product could not be saved. Please, try again.'));
        }
        $this->set(compact('product'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Product id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $id = $this->request->getData('id');
        $product = $this->Products->get($id);
        if ($this->Products->delete($product)) {
            $this->set([
                'message' => 'Product successfully deleted',
                'success' => true,
                '_serialize' => ['message', 'success']
            ]);
        } else {
            $this->set([
                'message' => 'Error while deleting product',
                'success' => false,
                '_serialize' => ['message', 'success']
            ]);
        }
    }
}
