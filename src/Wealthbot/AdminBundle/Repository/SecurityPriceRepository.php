<?php

namespace Wealthbot\AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Wealthbot\AdminBundle\Entity\Security;

/**
 * SecurityPriceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SecurityPriceRepository extends EntityRepository
{
    /**
     * Get current price by security_id
     *
     * @param int $securityId
     * @return mixed
     */
    public function getCurrentBySecurityId($securityId)
    {
        $qb = $this->createQueryBuilder('sp')
            ->where('sp.security_id = :security_id')
            ->andWhere('sp.is_current = :is_current')
            ->orderBy('sp.datetime', 'desc')
            ->setMaxResults(1)
            ->setParameters(array(
                'security_id' => $securityId,
                'is_current' => 1
            ));

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getPriceOnDate(Security $security, \DateTime $date)
    {
        $nextDay = new \DateTime();
        $nextDay->setTimestamp($date->getTimestamp());
        $nextDay->setTime(0, 0, 0);
        $nextDay->modify('+1 day');

        $qb = $this->createQueryBuilder('sp')
            ->where('sp.security = :security')
            ->andWhere('sp.datetime < :nextDay')
            ->setParameter('nextDay', $nextDay)
            ->setParameter('security', $security)
            ->setMaxResults(1)
            ->getQuery();

        return $qb->getOneOrNullResult();
    }

    /**
     * Set is_current flag to false for securities with security_id = $securityId
     *
     * @param int $securityId
     * @return mixed
     */
    public function resetIsCurrentFlagBySecurityId($securityId)
    {
        $qb = $this->createQueryBuilder('sp')
            ->update('WealthbotAdminBundle:SecurityPrice', 'sp')
            ->set('sp.is_current', 0)
            ->where('sp.security_id = :security_id')
            ->setParameter('security_id', $securityId);

        return $qb->getQuery()->execute();
    }
}