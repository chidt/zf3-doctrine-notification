<?php


namespace Application\Controller;


use Application\Entity\User;
use Zend\Authentication\AuthenticationService;
use Zend\Crypt\Password\Bcrypt;
use Zend\Http\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\View;

class UserController extends AbstractActionController
{
    /**
     * @var $authenticationService AuthenticationService
     */
    protected $authenticationService;

    /**
     * UserController constructor.
     * @param AuthenticationService $authenticationService
     */
    public function __construct(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    public function loginAction()
    {
        $layout = $this->layout();
        $layout->setTemplate('layout/login');
        /**
         * @var $request Request
         */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $adapter = $this->authenticationService->getAdapter();
            $adapter->setIdentity($data['username']);
            $adapter->setCredential($data['password']);
            $authResult = $this->authenticationService->authenticate();
            if ($authResult->isValid()) {
                $identity = $authResult->getIdentity();
                $this->authenticationService->getStorage()->write($identity);
                return $this->redirect()->toRoute('home');
            }
        } else {
            $view = new ViewModel();
            return $view;
        }


    }

    public function logoutAction()
    {
        if ($this->authenticationService->hasIdentity()) {
            $this->authenticationService->clearIdentity();
        }
    }

    public static function verifyCredential(User $user, $inputPassword)
    {
        return (new Bcrypt())->verify($inputPassword, $user->getPassword());
    }

}