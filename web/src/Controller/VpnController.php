<?php declare(strict_types=1);

namespace SohaJin\RedPacket2023\Controller;

use Doctrine\Persistence\ManagerRegistry;
use SohaJin\RedPacket2023\Entity\User;
use SohaJin\RedPacket2023\Entity\VpnApplication;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/vpn', name: 'vpn.')]
class VpnController extends AbstractController {
	private const STATEMENT_FLASH = 'vpn:application:statement';

	public function __construct(
		private readonly ManagerRegistry $doctrine
	) {}

	private function canUserApply(User $user): bool {
		if(!($application = $user->getLastApplication())) return true;
		return $application->getResult() === false;
	}

	#[Route('', name: 'index')]
	public function indexAction(Request $request, #[CurrentUser] User $user): Response {
		/** @var FlashBagAwareSessionInterface $session */
		$session = $request->getSession();
		return $this->render('vpn/index.html.twig', [
			'enabled' => $user->isVpnEnabled(),
			'canApply' => $this->canUserApply($user),
			'lastApplication' => $user->getLastApplication(),
			'lastStatement' => $session->getFlashBag()?->get(self::STATEMENT_FLASH)[0] ?? ''
		]);
	}

	#[Route('/apply', methods: ['POST'], name: 'apply')]
	public function applyAction(Request $request, #[CurrentUser] User $user): Response {
		$oldStatement = $statement = $request->request->get('statement', '');
		$statementLen = mb_strlen($statement);
		$statement = preg_replace('/<\s*\/?\s*script(\s*|\s+[^>]*)\/?>/i', '', $statement);
		$statement = preg_replace_callback(
			'/<\s*\S*(\s*|\s+[^>]*)\/?>/i',
			fn($matches) => preg_replace('/on[a-z]*\s*=\s*[\'"]?[^\'">]+[\'"]?/i', '', $matches[0]),
			$statement
		);
		$statement = trim($statement);
		$err = '';
		if($user->isVpnEnabled()) {
			$err = 'VPN 功能已经启用，无需申请';
		} else if(!$this->canUserApply($user)) {
			$err = '已有进行中的申请';
		} else if($statementLen < 10) {
			$err = '申请内容过短';
		} else if($statementLen > 1000) {
			$err = '申请内容过长';
		}
		if(!empty($err)) {
			$this->addFlash(self::STATEMENT_FLASH, $oldStatement);
			return $this->render('alert-jump.html.twig', [
				'message' => $err,
				'to' => 'vpn.index'
			]);
		}
		$application = (new VpnApplication())
			->setUser($user)
			->setStatement($statement)
		;
		$this->doctrine->getManager()->persist($application);
		$this->doctrine->getManager()->flush();
		return $this->render('alert-jump.html.twig', [
			'message' => '提交成功，管理员会马上审核',
			'to' => 'vpn.index'
		]);
	}
}
