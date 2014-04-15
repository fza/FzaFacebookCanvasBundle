<?php

namespace Fza\FacebookCanvasAppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class FacebookUserRepository extends EntityRepository
{
    /**
     * @param int $facebookUserId
     *
     * @return FacebookUser
     */
    public function findOrCreateUser($facebookUserId)
    {
        if (null === ($facebookUser = $this->findOneBy(array('id' => $facebookUserId)))) {
            $facebookUser = $this->getClassMetadata()->getReflectionClass()->newInstance();
            $facebookUser->setId($facebookUserId);
        }

        return $facebookUser;
    }
}
