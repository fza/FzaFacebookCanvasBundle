<?php

namespace Fza\FacebookCanvasAppBundle\Facebook;

class FacebookSessionPersistence extends \BaseFacebook
{
    const PREFIX = '_fza';

    private $session;

    private $prefix;

    protected static $kSupportedKeys = array('state', 'code', 'access_token', 'user_id');

    /**
     * @param array           $config
     * @param FacebookSession $facebookSession
     * @param string          $prefix
     */
    public function __construct($config, FacebookSession $facebookSession, $prefix = self::PREFIX)
    {
        $this->facebookSession = $facebookSession;
        $this->prefix          = $prefix;

        parent::__construct($config);
    }

    /**
     * @param string $key
     * @param array  $value
     */
    protected function setPersistentData($key, $value)
    {
        if (!in_array($key, self::$kSupportedKeys)) {
            self::errorLog('Unsupported key passed to setPersistentData.');

            return;
        }

        $this->facebookSession->set($this->constructSessionVariableName($key), $value);
    }

    /**
     * @param string $key
     * @param bool   $default
     *
     * @return bool|mixed|null
     */
    protected function getPersistentData($key, $default = false)
    {
        if (!in_array($key, self::$kSupportedKeys)) {
            self::errorLog('Unsupported key passed to getPersistentData.');

            return $default;
        }

        return $this->facebookSession->get($sessionVariableName, $default);
    }

    /**
     * @param string $key
     */
    protected function clearPersistentData($key)
    {
        if (!in_array($key, self::$kSupportedKeys)) {
            self::errorLog('Unsupported key passed to clearPersistentData.');

            return;
        }

        $this->facebookSession->remove($this->constructSessionVariableName($key));
    }

    protected function clearAllPersistentData()
    {
        foreach ($this->facebookSession->all() as $k => $v) {
            if (0 !== strpos($k, $this->prefix)) {
                continue;
            }

            $this->facebookSession->remove($k);
        }
    }

    /**
     * @param $key
     *
     * @return string
     */
    protected function constructSessionVariableName($key)
    {
        return $this->prefix . '_' . $this->getAppId() . '_' . $key;
    }
}
