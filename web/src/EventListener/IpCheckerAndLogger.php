<?php declare(strict_types=1);

namespace SohaJin\RedPacket2023\EventListener;

use Doctrine\Persistence\ManagerRegistry;
use PhpIP\IP;
use PhpIP\IPBlock;
use SohaJin\RedPacket2023\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(KernelEvents::REQUEST, 'onRequest')]
class IpCheckerAndLogger {
	public const SESSION_FIRST_ACCESS_TIME = 'firstAccessTime';
	public const REQUEST_IN_INTERNAL = 'ipInInternal';
	public const REQUEST_IN_OFFICE = 'ipInOffice';

	public function __construct(
		#[Autowire('@cidr.internal')] private readonly IPBlock $cidrInternal,
		#[Autowire('@cidr.office')] private readonly IPBlock $cidrOffice,
		private readonly Security $security,
		private readonly ManagerRegistry $doctrine
	) {}

	public function onRequest(RequestEvent $event): void {
		if(!$event->isMainRequest()) return;
		$request = $event->getRequest();
		/** @var User $user */
		if(!($user = $this->security->getUser()) instanceof User) {
			$session = $request->getSession();
			if(!$session->has(self::SESSION_FIRST_ACCESS_TIME)) {
				$session->set(self::SESSION_FIRST_ACCESS_TIME, time());
			}
			return;
		}
		if(!($ip = $this->getRequestIp($request))) return;
		$updated = false;
		if($this->cidrInternal->containsIP($ip)) {
			$request->attributes->set(self::REQUEST_IN_INTERNAL, true);
			if(!$user->getInternalAccessTime()) {
				$user->setInternalAccessIp($ip);
				$user->setInternalAccessTime(new \DateTimeImmutable());
				$updated = true;
			}
		}
		if($this->cidrOffice->containsIP($ip)) {
			$request->attributes->set(self::REQUEST_IN_OFFICE, true);
			if(!$user->getOfficeAccessTime()) {
				$user->setOfficeAccessIp($ip);
				$user->setOfficeAccessTime(new \DateTimeImmutable());
				$updated = true;
			}
		}
		if($updated) {
			$this->doctrine->getManager()->persist($user);
			$this->doctrine->getManager()->flush();
		}
	}

	private function getRequestIp(Request $request): ?IP {
		if(empty($ipString = $request->getClientIp())) {
			return null;
		}
		try {
			return IP::create($ipString);
		} catch(\InvalidArgumentException) {
			return null;
		}
	}
}
