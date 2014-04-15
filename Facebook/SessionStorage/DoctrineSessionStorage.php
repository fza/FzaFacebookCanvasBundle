<?php

namespace Fza\FacebookCanvasAppBundle\Facebook\SessionStorage;

use Doctrine\ORM\EntityManager;
use Fza\FacebookCanvasAppBundle\Entity\FacebookSession;

class DoctrineSessionStorage implements SessionStorageInterface
{
    private $started = false;

    private $em;

    private $repository;

    /** @var  \Fza\FacebookCanvasAppBundle\Facebook\FacebookSession */
    private $sessionObj;

    private $lifetime;

    private $data;

    /**
     * @param EntityManager $em
     * @param float         $gcProbability
     * @param int           $lifetime
     */
    public function __construct(EntityManager $em, $gcProbability = 0.2, $lifetime = 3600)
    {
        $this->em         = $em;
        $this->repository = $this->em->getRepository('FzaFacebookCanvasAppBundle:FacebookSession');

        $this->lifetime = $lifetime;
        $this->data     = array();

        if ($gcProbability && mt_rand(0, 100) / 100 >= $gcProbability) {
            $this->sessionGC();
        }
    }

    /**
     * @param string $accessToken
     */
    public function createSession($accessToken)
    {
        $this->sessionObj = new FacebookSession();
        do {
            $id = hash('sha1', uniqid(mt_rand() . hash('md5', mt_rand()), true));
        } while ($this->repository->findOneById($id));

        $this->sessionObj->setId($id);
        $this->sessionObj->setAccessToken($accessToken);
        $this->sessionObj->renewLastUpdated();

        $this->em->persist($this->sessionObj);
        $this->em->flush();

        $this->data = array();

        $this->started = true;
    }

    public function loadByAccessToken($accessToken)
    {
        if (null !== ($this->sessionObj = $this->repository->findOneByAccessToken($accessToken))) {
            $this->data = $this->sessionObj->getData();

            $this->started = true;

            return true;
        }

        return false;
    }

    /**
     * @param string $sessionId
     *
     * @return bool
     */
    public function loadBySessionId($sessionId)
    {
        if (null !== ($this->sessionObj = $this->repository->findOneById($sessionId))) {
            $this->data = $this->sessionObj->getData();

            $this->started = true;

            return true;
        }

        return false;
    }

    public function removeSession()
    {
        if (true === $this->started) {
            $this->em->remove($this->sessionObj);
            $this->em->flush();

            $this->data = array();

            $this->started = false;
        }
    }

    function sessionGC()
    {
        $timestamp = new \DateTime();
        $timestamp->modify('-' . $this->lifetime . ' seconds');

        $this->em->createQuery('DELETE FROM FzaFacebookCanvasAppBundle:FacebookSession fbs WHERE fbs.updated < :updated')
            ->setParameter('updated', $timestamp)
            ->getResult();
    }

    /**
     * @return null|string
     */
    function getSessionId()
    {
        if (true === $this->started) {
            return $this->sessionObj->getId();
        }

        return null;
    }

    /**
     * @return null|string
     */
    function getAccessToken()
    {
        if (true === $this->started) {
            return $this->sessionObj->getAccessToken();
        }

        return null;
    }

    /**
     * @param string $name
     * @param null   $default
     *
     * @return null
     */
    function read($name, $default = null)
    {
        if (true === $this->started) {
            return array_key_exists($name, $this->data) ? $this->data[$name] : $default;
        }

        return null;
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    function write($name, $value)
    {
        if (true === $this->started) {
            $this->data[$name] = $value;

            $this->sessionObj->setData($this->data);
            $this->sessionObj->renewLastUpdated();

            $this->em->flush();
        }
    }

    function isStarted()
    {
        return $this->started;
    }
}
