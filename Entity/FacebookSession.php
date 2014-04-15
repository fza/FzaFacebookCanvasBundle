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

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return null|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return null|string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    public function renewLastUpdated()
    {
        $this->updated = new \DateTime();
    }

    /**
     * @return null|\DateTime
     */
    public function getLastUpdated()
    {
        return $this->updated;
    }
}
