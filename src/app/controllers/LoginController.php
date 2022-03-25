<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;
use Phalcon\Http\Response;

class LoginController extends Controller
{
    public function indexAction()
    {
        //return '<h1>Hello!!!</h1>';
        $request = new Request();
        $user = new Users();
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        if ($this->cookies->has("remember")) {
            header('location:dashboard');
        } else {

        if ($this->request->isPost()) {

            if (empty($email) || empty($password)) {
                $response = new Response();
                $response->setStatusCode(404, 'Field Empty');
                $response->send();
                // echo "please fill all detials";
                $this->session->set('login', 'Please fill all details');
                // die();
            } else {
                $user = Users::findFirst(array(
                    'email = :email: and password = :password:', 'bind' => array(
                        'email' => $this->request->getPost("email"),
                        'password' => $this->request->getPost("password")
                    )
                ));

                if (!$user) {
                    $response = new Response();
                    $response->setStatusCode(403, 'Authentication Failed');
                    $response->send();
                    // echo "user not found";
                    $this->session->set('login', 'User not found');
                    // die();
                } else {
                    if(isset($_POST['remember'])) {
                        $this->cookies->set( 
                            "remember", 
                            json_encode([
                                'email' => $_POST['email'],
                                'password' => $_POST['password']
                            ]),
                            time() + 15 * 86400 
                         ); 
                    }
                    $this->session->set('userE', $email);
                    $this->session->set('userP', $password);
                    $this->session->set('login', '');
                    header("location:/dashboard");
                }
            }
        }
    }
}

    public function logoutAction()
    {
        // echo $this->session->get('userE'); to disdlay user email
        // echo$this->session->get('userP');  to disdlay user password

        $this->session->remove('userE');
        unset($this->session->email);
        $this->session->remove('userP');
        unset($this->session->password);

        // echo "after unset";
        // echo $this->session->get('userE');
        // echo$this->session->get('userP');

        // $session->destroy();
        $this->cookies->get('remember')->delete();
        $this->response->redirect('login');
    }
}
