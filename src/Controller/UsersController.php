<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Network\Exception\UnauthorizedException;
use Cake\Utility\Security;
use Firebase\JWT\JWT;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
	public function initialize()
    {
        parent::initialize();
        $this->Auth->allow(['register', 'login', 'logout']);
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);				
    }

    public function index()
    {
        $users = $this->paginate($this->Users);

        $this->set(compact('users'));
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);

        $this->set('user', $user);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function register()
    {
		$user = $this->Users->newEntity();
		$data = '';
		$success = false;

        if ($this->request->is('post')) {

            $user = $this->Users->patchEntity($user, $this->request->getData());

            if ($this->Users->save($user)) {
                $id = $user->id;
				$token = JWT::encode(['id' => $user->id, 'exp' =>  time() + 604800], Security::salt());
				$success = true;
				$this->set(['data' => [
				    			'id' => $id,
								'token' => $token],
							'success' => $success,
				            '_serialize' => ['data', 'success']
				]);
            } else {
				$this->set(['error' => $user->errors(), 'success' => $success, '_serialize' => ['error','success']]);
			}
        }
    }

    public function login()
    {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->set([
					'success' => true,
					'data' => [
						'token' => JWT::encode([
						    'id' => $user['id'],
							'exp' =>  time() + 604800
						    ],
                            Security::salt()),
                        'name' => $user['name']
					],
					'_serialize' => ['success', 'data']
				]);
            } else {
                // throw new UnauthorizedException('Invalid username or password');
                $this->set([
                    'success' => false,
                    'message' => 'Invalid username or password',
					'_serialize' => ['success', 'message']
				]);
            }
        }
    }

    public function logout()
    {
        $this->Auth->logout();
        $this->Flash->success(__('You have successfully logged out'));
        $this->redirect(['action' => 'login']);
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function dashboard()
    {

    }
}
