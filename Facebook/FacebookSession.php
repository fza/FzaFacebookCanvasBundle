<?php

namespace Fza\FacebookCanvasAppBundle\Facebook;

use Fza\FacebookCanvasAppBundle\Facebook\SessionStorage\SessionStorageInterface;

class FacebookSession
{
    protected $storage;
    protected $facebook;
    protected $started = false;
    protected $attributes = array();

    public function __construct( SessionStorageInterface $storage )
    {
        $this->storage = $storage;
    }

    public function startWithAccessToken( $accessToken )
    {
        if( false === $this->storage->loadByAccessToken( $accessToken ) )
        {
            $this->storage->createSession( $accessToken );
        }

        $this->started = true;
    }

    public function startWithSessionId( $sessionId )
    {
        $this->storage->loadBySessionId( $sessionId );

        if( $this->storage->isStarted() )
        {
            $this->attributes = $this->storage->read( 'attributes' );
            $this->started = true;
        }
    }

    public function has( $name )
    {
        if( false === $this->started )
        {
            return null;
        }

        return array_key_exists( $name, $this->attributes );
    }

    public function get( $name, $default = null )
    {
        if( false === $this->started )
        {
            return $default;
        }

        return array_key_exists( $name, $this->attributes ) ? $this->attributes[$name] : $default;
    }

    public function set( $name, $value )
    {
        if( true === $this->started )
        {
            $this->attributes[$name] = $value;
        }
    }

    public function all()
    {
        if( false === $this->started )
        {
            return array();
        }

        return $this->attributes;
    }

    public function remove( $name )
    {
        if( true === $this->started && array_key_exists( $name, $this->attributes ) )
        {
            unset( $this->attributes[$name] );
        }
    }

    public function clear()
    {
        $this->attributes = array();
    }

    public function invalidate()
    {
        $this->storage->removeSession();
        $this->attributes = array();
        $this->started = false;
    }

    public function getSessionId()
    {
        if( false === $this->started )
        {
            return null;
        }

        return $this->storage->getSessionId();
    }

    public function getAccessToken()
    {
        if( false === $this->started )
        {
            return null;
        }

        return $this->storage->getAccessToken();
    }

    public function isStarted()
    {
        return $this->started;
    }

    public function save()
    {
        if( false === $this->started )
        {
            return null;
        }

        $this->storage->write( 'attributes', $this->attributes );
    }

    public function __destruct()
    {
        $this->save();
    }
}
