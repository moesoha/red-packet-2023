<?php declare(strict_types=1);

namespace SohaJin\RedPacket2023\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SohaJin\RedPacket2023\Entity\VpnApplication;

/**
 * @method VpnApplication|null find($id, $lockMode = null, $lockVersion = null)
 * @method VpnApplication|null findOneBy(array $criteria, array $orderBy = null)
 * @method VpnApplication[]    findAll()
 * @method VpnApplication[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VpnApplicationRepository extends ServiceEntityRepository {
	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, VpnApplication::class);
	}
}
