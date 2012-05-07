<?php

namespace Fza\FacebookCanvasAppBundle\Facebook;

use Fza\FacebookCanvasAppBundle\Entity\FacebookUser;
use Symfony\Component\HttpFoundation\Request;

class FacebookContext
{
    protected $facebookSession;
    protected $facebookBase;

    protected $config = array();

    protected $isSignedRequest = false;
    protected $pageData = null;

    protected $initialized = false;

    public function __construct( FacebookSession $facebookSession, \BaseFacebook $facebookBase, array $config )
    {
        $this->facebookSession = $facebookSession;
        $this->facebookBase = $facebookBase;

        $this->config = $config;
    }

    /**
     * Initializes a facebook session wether there is one or not
     */
    public function initialize( Request $request )
    {
        if( $this->initialized )
        {
            return;
        }

        // First check if the Facebook SDK has something for us.
        if( $signedRequest = $this->facebookBase->getSignedRequest() )
        {
            $this->isSignedRequest = true;
            $this->pageData = isset( $signedRequest['page'] ) ? $signedRequest['page'] : null;

            $this->facebookSession->startWithAccessToken( $this->facebookBase->getAccessToken() );

            try
            {
                $this->readUserProfile();
            }
            catch( \Exception $e )
            {}
        }

        // Then check eventually provided request parameters. Our last chance is to look at the session cookie, but that will only work
        // if the user's browser has third party cookies enabled, which is hardly the case.
        else if(    ( $this->config['session_use_parameter'] && null !== ( $sessionId = $request->attributes->get( $this->config['session_parameter_key'] ) ) )
                 || ( $this->config['session_use_request']   && null !== ( $sessionId = $request->request->get( $this->config['session_request_key'] ) ) )
                 || ( $this->config['session_use_query']     && null !== ( $sessionId = $request->query->get( $this->config['session_query_key'] ) ) )
                 || ( null !== ( $sessionId = $request->getSession()->get( '_fza_facebookapp_fb_session_id' ) ) ) )
        {
            $this->facebookSession->startWithSessionId( $sessionId );

            if( true === $this->facebookSession->isStarted() )
            {
                $this->facebookBase->setAccessToken( $this->facebookSession->getAccessToken() );

                // We need to check if the derived access token is still valid
                try
                {
                    $this->readUserProfile();
                }
                catch( \FacebookApiException $e )
                {
                    $this->facebookSession->invalidate();
                }
            }
        }

        // Initialized does not mean that this is a valid access to our app, but but it means that we did everything
        // to authenticate the user or at least to get the current facebook session.
        $this->initialized = true;
    }

    public function getShowdownDate()
    {
        return array_key_exists( 'showdown_date', $this->config ) ? new \DateTime( $this->config['showdown_date'] ) : null;
    }

    public function isSignedRequest()
    {
        if( false === $this->initialized )
        {
            $this->initialize();
        }

        return $this->isSignedRequest;
    }

    public function isPageLiked()
    {
        if( false === $this->initialized )
        {
            $this->initialize();
        }

        return null !== $this->pageData && array_key_exists( 'liked', $this->pageData ) ? $this->pageData['liked'] : null;
    }

    public function getAppId()
    {
        return $this->facebookBase->getAppId();
    }

    public function getApiSecret()
    {
        return $this->facebookBase->getApiSecret();
    }

    public function getPageId()
    {
        return $this->config['page_id'];
    }

    public function getAccessToken()
    {
        if( false === $this->initialized )
        {
            $this->initialize();
        }

        if( $this->facebookSession->isStarted() )
        {
            return $this->facebookSession->getAccessToken();
        }

        return null;
    }

    public function getSessionId()
    {
        if( false === $this->initialized )
        {
            $this->initialize();
        }

        if( $this->facebookSession->isStarted() )
        {
            return $this->facebookSession->getSessionId();
        }

        return null;
    }

    /**
     * Upon successfull page request the current facebook user entity
     * can be derived through the "facebook.user" service.
     */
    public function getUserId()
    {
        if( false === $this->initialized )
        {
            $this->initialize();
        }

        if( $this->facebookSession->isStarted() )
        {
            $facebookUserId = $this->facebookBase->getUser();

            // The Facebook SDK might change, so we check all possibilites...
            if( 0 === $facebookUserId || false === $facebookUserId || null === $facebookUserId )
            {
                return null;
            }

            return $facebookUserId;
        }

        return null;
    }

    /**
     * Read the user profile
     */
    public function readUserProfile()
    {
        // Only call the Facebook Graph if we haven't done so before while this session is active
        if( $this->facebookSession->isStarted() && null === $this->facebookSession->get( 'profile' ) )
        {
            $profileData = $this->facebookBase->api( '/me' );

            $this->facebookSession->set( 'profile', $profileData );
            $this->facebookSession->set( 'locale', strtolower( substr( $profileData['locale'], 0, strpos( $profileData['locale'], '_' ) ) ) );
            $this->facebookSession->set( 'firstname', $profileData['first_name'] );
        }
    }
}
