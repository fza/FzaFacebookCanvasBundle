<?php

namespace Fza\FacebookCanvasAppBundle\Facebook;

use Fza\FacebookCanvasAppBundle\Facebook\SessionStorage\SessionStorageInterface;

class FacebookSession
{
    protected $storage;

    protected $facebook;

    protected $started = false;

    protected $attributes = array();

    public function __construct(SessionStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * First look up if we have a session which is connected to this access token.
     * If there is none, we create a new session.
     */
    public function startWithAccessToken($accessToken)
    {
        if (false === $this->storage->loadByAccessToken($accessToken)) {
            $this->storage->createSession($accessToken);
        }

        $this->started = true;
    }

    /**
     * Load an existing session by its id.
     *
     * @param string $sessionId
     */
    public function startWithSessionId($sessionId)
    {
        $this->storage->loadBySessionId($sessionId);

        if ($this->storage->isStarted()) {
            $this->attributes = $this->storage->read('attributes');
            $this->started    = true;
        }
    }

    /**
     * @param $name
     *
     * @return bool|null
     */
    public function has($name)
    {
        if (false === $this->started) {
            return null;
        }

        return array_key_exists($name, $this->attributes);
    }

    /**
     * @param string $name
     * @param null $default
     *
     * @return null
     */
    public function get($name, $default = null)
    {
        if (false === $this->started) {
            return $default;
        }

        return array_key_exists($name, $this->attributes) ? $this->attributes[$name] : $default;
    }

    /**
     * @param $name
     * @param $value
     */
    public function set($name, $value)
    {
        if (true === $this->started) {
            $this->attributes[$name] = $value;
        }
    }

    /**
     * @return array
     */
    public function all()
    {
        if (false === $this->started) {
            return array();
        }

        return $this->attributes;
    }

    /**
     * @param string $name
     */
    public function remove($name)
    {
        if (true === $this->started && array_key_exists($name, $this->attributes)) {
            unset($this->attributes[$name]);
        }
    }

    public function clear()
    {
        $this->attributes = array();
    }

    /**
     * @return null
     */
    public function invalidate()
    {
        $this->storage->removeSession();
        $this->attributes = array();
        $this->started    = false;
    }

    /**
     * @return null
     */
    public function getSessionId()
    {
        if (false === $this->started) {
            return null;
        }

        return $this->storage->getSessionId();
    }

    /**
     * @return null|string
     */
    public function getAccessToken()
    {
        if (false === $this->started) {
            return null;
        }

        return $this->storage->getAccessToken();
    }

    /**
     * @return bool
     */
    public function isStarted()
    {
        return $this->started;
    }

    /**
     * @return null
     */
    public function save()
    {
        if (false === $this->started) {
            return null;
        }

        $this->storage->write('attributes', $this->attributes);
    }

    /**
     * @return null
     */
    public function __destruct()
    {
        $this->save();
    }
}
