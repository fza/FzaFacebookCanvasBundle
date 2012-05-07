<?php

namespace Fza\FacebookCanvasAppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class FacebookUserRepository extends EntityRepository
{
    public function findOrCreateUser( $facebookUserId, $skipCheck = false )
    {
        $em = $this->getEntityManager();

        if( null === ( $facebookUser = $this->findOneById( $facebookUserId ) ) )
        {
            $facebookUser = $this->getClassMetadata()->getReflectionClass()->newInstance();
            $facebookUser->setId( $facebookUserId );
        }

        return $facebookUser;
    }
}

?>