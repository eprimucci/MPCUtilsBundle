<?php

namespace CodigoAustral\MPCUtilsBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of ObservatoryRepository
 *
 * @author piri
 */
class ObservatoryRepository extends EntityRepository {

    public function findFirstObservatoryInDownloadQueue($date) {
        
        
        return $this->createQueryBuilder('o')
                ->where('o.lastObsDownload < :date')
                ->andWhere('o.downloadPriority >= 0')
                ->setParameter('date', $date)
                ->orderBy('o.lastObsDownload, o.downloadPriority')
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();
    }

}
