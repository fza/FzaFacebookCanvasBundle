<?php

namespace Fza\FacebookCanvasAppBundle\Facebook;

class FacebookSessionPersistence extends \BaseFacebook
{
    const PREFIX = '_fza';

    private $session;
    private $prefix;
    protected static $kSupportedKeys = array( 'state', 'code', 'access_token', 'user_id' );

    public function __construct( $config, FacebookSession $facebookSession, $prefix = self::PREFIX )
    {
        $this->facebookSession = $facebookSession;
        $this->prefix  = $prefix;

        parent::__construct( $config );
    }

    protected function setPersistentData( $key, $value )
    {
        if( !in_array( $key, self::$kSupportedKeys ) )
        {
            self::errorLog( 'Unsupported key passed to setPersistentData.' );
            return;
        }

        $this->facebookSession->set( $this->constructSessionVariableName( $key ), $value );
    }

    protected function getPersistentData( $key, $default = false )
    {
        if( !in_array( $key, self::$kSupportedKeys ) )
        {
            self::errorLog( 'Unsupported key passed to getPersistentData.' );
            return $default;
        }

        return $this->facebookSession->get( $sessionVariableName, $default );
    }

    protected function clearPersistentData( $key )
    {
        if( !in_array( $key, self::$kSupportedKeys ) )
        {
            self::errorLog( 'Unsupported key passed to clearPersistentData.' );
            return;
        }

        $this->facebookSession->remove( $this->constructSessionVariableName( $key ) );
    }

    protected function clearAllPersistentData()
    {
        foreach( $this->facebookSession->all() as $k => $v )
        {
            if( 0 !== strpos( $k, $this->prefix ) )
            {
                continue;
            }

            $this->facebookSession->remove( $k );
        }
    }

    protected function constructSessionVariableName( $key )
    {
        return $this->prefix . '_' . $this->getAppId() . '_' . $key;
    }
}
