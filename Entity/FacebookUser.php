<?php

namespace Fza\FacebookCanvasAppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class FacebookUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @ORM\Index;
     * @ORM\Column(type="string")
     */
    private $accessToken;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return null|int
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
     * @return null|\DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreated()
    {
        if (null === $this->created) {
            $this->created = new \DateTime();
        }
    }
}
