<?php

namespace Fza\FacebookCanvasAppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="facebookSession",uniqueConstraints={@ORM\UniqueConstraint(name="access_token_idx", columns={"accessToken"})})
 */
class FacebookSession
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length="40")
     */
    private $id;

    /**
     * @ORM\Index;
     * @ORM\Column(type="string")
     */
    private $accessToken;

    /**
     * @ORM\Column(type="array")
     */
    private $data = array();

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated;

    public function setId( $id )
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setAccessToken( $accessToken )
    {
        $this->accessToken = $accessToken;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function setData( array $data )
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function renewLastUpdated()
    {
        $this->updated = new \DateTime();
    }

    public function getLastUpdated()
    {
        return $this->updated;
    }
}