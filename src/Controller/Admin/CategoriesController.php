<?php
namespace App\Controller\Admin;

use App\Controller\AppController;

/**
 * Categories Controller
 *
 * @property \App\Model\Table\CategoriesTable $Categories
 *
 * @method \App\Model\Entity\Category[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CategoriesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $ctgArr = array();
        $categories = $this->Categories->find('all');
        $cnt = 1;
		foreach($categories as $category) {
			$ctgArr[] = array(
				'position' => $cnt,
				'category' => $category->category_name,
				'id' => $category->id
			);
			$cnt++;
        }

        $this->set([
            'ctgArr' => $ctgArr,
            'success' => true,
            '_serialize' => ['ctgArr', 'success']
            ]);
    }

    function getCategoriesArr() {
        $categories = $this->Categories->find('list', [
            'keyField' => 'id',
            'valueField' => 'category_name'
        ]);
        $ctg_arr = array();
        foreach ($categories as $key => $value) {
            $ctg_arr[] = array('id' => $key, 'value' => $value);
        }

        $this->set([
            'ctgArr' => $ctg_arr,
            'success' => true,
            '_serialize' => ['ctgArr', 'success']
            ]);
    }

    /**
     * View method
     *
     * @param string|null $id Category id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->viewBuilder()->setLayout('admin');
        $category = $this->Categories->get($id, [
            'contain' => []
        ]);

        $this->set('category', $category);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $category = $this->Categories->newEntity();
        if ($this->request->is('post')) {
            $category = $this->Categories->patchEntity($category, $this->request->getData());
            if ($this->Categories->save($category)) {
                $this->set([
                    'message' => 'Category added successfully',
                    'success' => true,
                    '_serialize' => ['message', 'success']
                    ]);
            }else {
                $this->set([
                    'message' => 'Error while adding category',
                    'success' => false,
                    '_serialize' => ['message', 'success']
                ]);
            }
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Category id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->viewBuilder()->setLayout('admin');
        $category = $this->Categories->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $category = $this->Categories->patchEntity($category, $this->request->getData());
            if ($this->Categories->save($category)) {
                $this->Flash->success(__('The category has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The category could not be saved. Please, try again.'));
        }
        $this->set(compact('category'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Category id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $id = $this->request->getData('id');
        $category = $this->Categories->get($id);
        if ($this->Categories->delete($category)) {
            $this->set([
                'message' => 'Category successfully deleted',
                'success' => true,
                '_serialize' => ['message', 'success']
            ]);
        } else {
            $this->set([
                'message' => 'Error while deleting category',
                'success' => false,
                '_serialize' => ['message', 'success']
            ]);
        }
    }
}
