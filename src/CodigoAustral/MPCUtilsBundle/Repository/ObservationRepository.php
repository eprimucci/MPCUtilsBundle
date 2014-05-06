<?php

namespace CodigoAustral\MPCUtilsBundle\Repository;

use Doctrine\ORM\EntityRepository;
use CodigoAustral\MPCUtilsBundle\Entity\Observatory;

/**
 *
 * @author piri
 */
class ObservationRepository extends EntityRepository {

    public function statsForObservatory(Observatory $observatory) {
        
        
        return $this->createQueryBuilder('os')
                ->where('os.observatory = :observatory')
                ->andWhere('o.downloadPriority >= 0')
                ->setParameter('observatory', $observatory)
                ->orderBy('o.lastObsDownload, o.downloadPriority')
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();
    }

    
}
