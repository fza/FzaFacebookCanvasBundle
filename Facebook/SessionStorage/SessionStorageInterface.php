<?php

namespace Fza\FacebookCanvasAppBundle\Facebook\SessionStorage;

interface SessionStorageInterface
{
    function createSession($accessToken);

    function loadByAccessToken($accessToken);

    function loadBySessionId($sessionId);

    function removeSession();

    function sessionGC();

    function getSessionId();

    function getAccessToken();

    function read($key);

    function write($key, $data);
}
