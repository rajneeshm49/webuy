<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller\Admin;

use App\Controller\AppController;

use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Event\Event;
use Cake\Network\Exception\UnauthorizedException;
use Cake\Utility\Security;
use Firebase\JWT\JWT;
/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class UsersController extends AppController
{

    /**
     * Displays a view
     *
     * @param array ...$path Path segments.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Http\Exception\ForbiddenException When a directory traversal attempt.
     * @throws \Cake\Http\Exception\NotFoundException When the view file could not
     *   be found or \Cake\View\Exception\MissingTemplateException in debug mode.
     */
   	
	public function initialize()
    {
        parent::initialize();
        $this->Auth->allow(['login', 'logout']);
    }

	public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
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
                //throw new UnauthorizedException('Invalid username or password');
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

    public function register()
    {
        $this->viewBuilder()->setLayout('admin');
    }

    public function dashboard()
    {
        $this->viewBuilder()->setLayout('admin');
    }
}
