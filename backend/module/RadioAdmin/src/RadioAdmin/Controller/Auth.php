<?php
namespace RadioAdmin\Controller;

use Radio\Entity\ChangePasswordToken;
use Radio\Entity\User;
use Radio\Provider\EntityManager;
use Zend\Mvc\Controller\AbstractActionController,
    Zend\View\Model\JsonModel,
    Radio\Provider\AuthService,
    Zend\Json\Json;
use Zend\Mail;
use Radio\Controller\BaseController;



class Auth extends BaseController
{

    use AuthService;

    use EntityManager;

    public function login()
    {
        if (!$this->getRequest()->isPost()) {
            $this->getResponse()->setStatusCode(400);
            return new JsonModel(array("error" => "Bad request: POST required"));
        }
        $data = Json::decode($this->getRequest()->getContent(), Json::TYPE_ARRAY);
        if (!array_key_exists('username', $data) || !array_key_exists('username', $data)) {
            $this->getResponse()->setStatusCode(400);
            return new JsonModel(array("error" => "Bad request: felhasználónév és jelszó szükséges."));
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
        return new JsonModel(array('success' => false, 'error' => "Hitelesítési hiba"));
    }

    public function logout()
    {
        if (!$this->getAuthService()->hasIdentity()) {
            $this->getResponse()->setStatusCode(400);
            return new JsonModel(array('success' => false, 'error' => "No valid session"));
        }
        $this->getAuthService()->clearIdentity();
        return $this->success();
    }

    private function success()
    {
        $identity = $this->getAuthService()->getIdentity();
        // identity shall never be null on success
        if (null !== $identity)
            $identity = $identity->toArraySafe();
        return new JsonModel(array('success' => true, 'data' => $identity));
    }

    private function failed($msg)
    {
        return new JsonModel(array('success' => false, 'error' => $msg));
    }

    public function passwordReset()
    {
        //slow it down
        sleep(1);
        $data = Json::decode($this->getRequest()->getContent(), Json::TYPE_ARRAY);
        if (strtolower($this->getRequest()->getMethod()) != 'post') {
            $this->getResponse()->setStatusCode(400);
            return new JsonModel(array("error" => "Use POST request."));
        }

        if (!array_key_exists('email', $data)) {
            $this->getResponse()->setStatusCode(400);
            return new JsonModel(array("error" => "Adj meg e-mail címet."));
        }


        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('u')->from('\Radio\Entity\User', 'u');
        $qb->where('u.email = :email');
        $q = $qb->getQuery();
        $q->setParameter("email", $data['email']);
        $user = $q->getResult();
        if (is_null($user) || count($user) != 1) {
            $qb = $this->getEntityManager()->createQueryBuilder();
            //try to create new user based on author
            $qb->select('a', 'u')->from('\Radio\Entity\Author', 'a');

            $qb->leftJoin("a.user", "u");
            $qb->where('a.email = :email');
            $q = $qb->getQuery();
            $q->setParameter("email", $data['email']);

            $authors = $q->getResult();


            if (count($authors) == 1) {
                $author = $authors[0];
		// PHP <5.5 workaround
		$authorObj = $author->getUser();
                if (empty($authorObj)) {
                    $user = new User();
                    $user->setEmail($author->getEmail());
                    $user->setUsername($author->getAlias());
                    $user->setRole($this->getEntityManager()->find("\Radio\Entity\Role", 3));
                    $this->getEntityManager()->persist($user);
                    $this->getEntityManager()->flush();
                    $author->setUser($user);
                    $this->getEntityManager()->persist($author);
                    $this->getEntityManager()->flush();
                }

            }
            if (empty($user)) {
                $this->getResponse()->setStatusCode(400);
                return new JsonModel(array("error" => "Ezt a mail-t nem ismerem."));

            }
        } else {
            $user = $user[0];
        }
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
            $link = str_replace("-front","-admin",$link);

            //sending mail
            $mail = new Mail\Message();
            $body = "A Te user-neved: \n\nA " . $user->getUsername() . "! Jelszavad megadhatod ezen a linken: " .
                $link;
            $mail->setBody($body);
            $mail->setFrom('webmester@tilos.hu', 'Tilos gépház');
            $mail->addTo($user->getEmail());
            $mail->setSubject('[tilos.hu] Jelszó emlékeztető');

            $transport = $this->getServiceLocator()->get('Radio\Mail\Transport');
            $transport->send($mail);

            return new JsonModel(array("success" => true, "message" => "Új belépőkód elküldve."));
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
                return new JsonModel(array("error" => "Ezen a címen nincs érvényes belépőkód."));
            }
            $token = $results[0];
            $now = new \DateTime();
            $now = $now->sub(new \DateInterval("PT30M"));
            if ($now->getTimestamp() > $results[0]['created']->getTimestamp()) {
                $this->getResponse()->setStatusCode(400);
                return new JsonModel(array("error" => "Ez a belépőkód már lejárt."));
            }

            if (!array_key_exists('password', $data)) {
                $this->getResponse()->setStatusCode(400);
                return new JsonModel(array("error" => "Üres a jelszó helye."));
            }

            $password = $data['password'];
            if (strlen($password) < 9) {
                $this->getResponse()->setStatusCode(400);
                return new JsonModel(array("error" => "A jelszó túl rövid (min 9 karakter)."));
            }
            $user->setPassword($password);
            $this->getEntityManager()->persist($user);
            $q = $this->getEntityManager()->createQueryBuilder()->delete('\Radio\Entity\ChangePasswordToken', 't')->where("t.user = :user")->getQuery();
            $q->setParameter("user", $user);
            $q->execute();
            $this->getEntityManager()->flush();
            return new JsonModel(array("success" => true, "message" => "a jelszó megváltoztatva"));
            //check token and change the password
        }
    }

    public function encode($str)
    {
        $str = urlencode($str);
        $str = str_replace('.', '%2E', $str);
        $str = str_replace('-', '%2D', $str);
        return $str;
    }
}
