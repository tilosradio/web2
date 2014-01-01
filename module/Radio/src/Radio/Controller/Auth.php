<?php
namespace Radio\Controller;

use Radio\Entity\ChangePasswordToken;
use Radio\Provider\EntityManager;
use Zend\Mvc\Controller\AbstractActionController,
    Zend\View\Model\JsonModel,
    Radio\Provider\AuthService,
    Zend\Json\Json;
use Zend\Mail;


class Auth extends BaseController {

    use AuthService;

    use EntityManager;

    public function loginAction() {
        if (!$this->getRequest()->isPost()) {
            $this->getResponse()->setStatusCode(400);
            return new JsonModel(array("error" => "Bad request"));
        }
        $data = Json::decode($this->getRequest()->getContent(), Json::TYPE_ARRAY);
        if (!array_key_exists('username', $data) || !array_key_exists('username', $data)) {
            $this->getResponse()->setStatusCode(400);
            return new JsonModel(array("error" => "Bad request: User and password is required."));
        }

        $adapter = $this->getAuthService()->getAdapter();
        $adapter->setIdentityValue($data['username']);
        $adapter->setCredentialValue($data['password']);
        $result = $adapter->authenticate();
        if ($result->isValid()) {
            $this->getAuthService()
                ->getStorage()
                ->write($result->getIdentity());
            return $this->success();
        } else
            $this->getResponse()->setStatusCode(401);
        return new JsonModel(array('success' => false, 'error' => "Authentication error"));
    }

    public function logoutAction() {
        if (!$this->getAuthService()->hasIdentity()) {
            $this->getResponse()->setStatusCode(400);
            return new JsonModel(array('success' => false, 'error' => "No valid session"));
        }
        $this->getAuthService()->clearIdentity();
        return $this->success();
    }

    private function success() {
        $identity = $this->getAuthService()->getIdentity();
        // identity shall never be null on success
        if (null !== $identity)
            $identity = $identity->toArraySafe();
        return new JsonModel(array('success' => true, 'data' => $identity));
    }

    private function failed($msg) {
        return new JsonModel(array('success' => false, 'error' => $msg));
    }

    public function passwordResetAction() {
        //slow it down
        sleep(1);
        $data = Json::decode($this->getRequest()->getContent(), Json::TYPE_ARRAY);
        if (strtolower($this->getRequest()->getMethod()) != 'post') {
            $this->getResponse()->setStatusCode(400);
            return new JsonModel(array("error" => "Use POST request."));
        }

        if (!array_key_exists('email', $data)) {
            $this->getResponse()->setStatusCode(400);
            return new JsonModel(array("error" => "email field is requried"));
        }


        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('u')->from('\Radio\Entity\User', 'u');
        $qb->where('u.email = :email');
        $q = $qb->getQuery();
        $q->setParameter("email", $data['email']);
        $user = $q->getResult();
        if (is_null($user) || count($user) != 1) {
            $this->getResponse()->setStatusCode(400);
            return new JsonModel(array("error" => "Email does not exist in DB."));
        }
        $user = $user[0];
        if (!array_key_exists('token', $data)) {
            $q = $this->getEntityManager()->createQueryBuilder()->delete('\Radio\Entity\ChangePasswordToken', 't')->where("t.user = :user")->getQuery();
            $q->setParameter("user", $user);
            $q->execute();

            //create password token
            $token = new ChangePasswordToken();
            $token->setCreated(new \DateTime());
            $token->setToken(sha1(date('YmdHis') . mt_rand() . mt_rand()));
            $token->setUser($user);
            $this->getEntityManager()->persist($token);
            $this->getEntityManager()->flush();

            $link = $this->getServerUrl() . "/password_reset?token=" . $this->encode($token->getToken()) . "&email=" . $this->encode($user->getEmail());


            //sending mail
            $mail = new Mail\Message();
            $body = "\n\nJelszó megváltoztatása a következő linken keresztül lehetséges: " . $link;
            $mail->setBody($body);
            $mail->setFrom('webmester@tilos.hu', 'Tilos gépház');
            $mail->addTo($user->getEmail());
            $mail->setSubject('[tilos.hu]Jelszó emlékeztető');


            $transport = $this->getServiceLocator()->get('Radio\Mail\Transport');
            $transport->send($mail);

            return new JsonModel(array("success" => true, "message" => "Token has been generated and sent."));
            //regenerate token and send it in a mail
        } else {
            $token = $data["token"];
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->select('t')->from('\Radio\Entity\ChangePasswordToken', 't');
            $qb->where('t.user = :usr AND t.token = :tkn');
            $qb->orderBy("t.created", "DESC");
            $q = $qb->getQuery();
            $q->setParameter("usr", $user);
            $q->setParameter("tkn", $token);
            $results = $q->getArrayResult();
            if (count($results) == 0) {
                $this->getResponse()->setStatusCode(400);
                return new JsonModel(array("error" => "No such valid token."));
            }
            $token = $results[0];
            $now = new \DateTime();
            $now = $now->sub(new \DateInterval("PT30M"));
            if ($now->getTimestamp() > $results[0]['created']->getTimestamp()) {
                $this->getResponse()->setStatusCode(400);
                return new JsonModel(array("error" => "Token is too old"));
            }

            if (!array_key_exists('password', $data)) {
                $this->getResponse()->setStatusCode(400);
                return new JsonModel(array("error" => "Password field is empty."));
            }

            $password = $data['password'];
            if (strlen($password) < 9) {
                $this->getResponse()->setStatusCode(400);
                return new JsonModel(array("error" => "Password is too short (min size: 9)."));
            }
            $user->setPassword($password);
            $this->getEntityManager()->persist($user);
            $q = $this->getEntityManager()->createQueryBuilder()->delete('\Radio\Entity\ChangePasswordToken', 't')->where("t.user = :user")->getQuery();
            $q->setParameter("user", $user);
            $q->execute();
            $this->getEntityManager()->flush();
            return new JsonModel(array("success" => true, "message" => "password has been changed"));
            //check token and change the password
        }
    }

    public function encode($str) {
        $str = urlencode($str);
        $str = str_replace('.', '%2E', $str);
        $str = str_replace('-', '%2D', $str);
        return $str;
    }
}
