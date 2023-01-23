<?php declare(strict_types=1);

namespace SohaJin\RedPacket2023\Controller;

use Doctrine\Persistence\ManagerRegistry;
use SohaJin\RedPacket2023\Entity\User;
use SohaJin\RedPacket2023\Entity\VpnApplication;
use SohaJin\RedPacket2023\Repository\VpnApplicationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/vpn', name: 'vpn.')]
class VpnController extends AbstractController {
	private const STATEMENT_FLASH = 'vpn:application:statement';

	public function __construct(
		private readonly VpnApplicationRepository $vpnApplicationRepository,
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

	#[Route('/resetPassword', name: 'reset_password', methods: ['POST'])]
	public function resetPasswordAction(#[CurrentUser] User $user): Response {
		if(!$user->isVpnEnabled()) {
			throw new AccessDeniedHttpException();
		}
		$user->generateVpnPassword();
		$this->doctrine->getManager()->persist($user);
		$this->doctrine->getManager()->flush();
		return $this->render('alert-jump.html.twig', [
			'message' => '密码重置成功，请使用新密码登录 VPN',
			'to' => 'vpn.index'
		]);
	}

	#[Route('/config/ovpn', name: 'config.ovpn')]
	public function configOvpnAction(#[CurrentUser] User $user, #[Autowire('%vpn.client_config%')] string $ovpnConfig): Response {
		if(!$user->isVpnEnabled()) {
			throw new AccessDeniedHttpException();
		}
		return new Response($ovpnConfig, headers: [
			'Content-Type' => 'application/octet-stream',
			'Content-Disposition' => 'attachment; filename="tnhbyjs-vpn.ovpn"'
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
			'to' => 'vpn.show_application',
			'toParam' => ['id' => $application->getId()]
		]);
	}

	#[Route('/application/{id}', name: 'show_application')]
	public function showAction(int $id, UserInterface $user): Response {
		if(!$id || !($application = $this->vpnApplicationRepository->find($id))) {
			throw new NotFoundHttpException();
		}
		$isReviewer = $this->isGranted('ROLE_REVIEWER');
		if(!$isReviewer && $user !== $application->getUser()) {
			throw new AccessDeniedHttpException();
		}

		return $this->render('vpn/application.html.twig', [
			'isReviewer' => $isReviewer,
			'application' => $application
		]);
	}

	#[Route('/review/pending', name: 'review.pending')]
	public function reviewerAllUnreviewedAction(): Response {
		$this->denyAccessUnlessGranted('ROLE_REVIEWER');
		return $this->json(array_map(
			fn(VpnApplication $a) => $a->getId(),
			$this->vpnApplicationRepository->findUnreviewed()
		));
	}

	#[Route('/review', methods: ['POST'], name: 'review.action')]
	public function reviewerReviewAction(Request $request): Response {
		$this->denyAccessUnlessGranted('ROLE_REVIEWER');
		if(
			!($id = $request->request->getInt('id')) ||
			!($application = $this->vpnApplicationRepository->find($id))
		) throw new NotFoundHttpException();

		if($application->getResult() === null) {
			$accept = $request->request->get('action', 'reject') === 'accept';
			$application->setResult($accept);
			$this->doctrine->getManager()->persist($application);
			if($accept) {
				$user = $application->getUser();
				$user->generateVpnPassword();
				$this->doctrine->getManager()->persist($user);
			}
			$this->doctrine->getManager()->flush();
			return $this->json(['_ok' => 1]);
		}

		return $this->json(['_ok' => 0]);
	}
}
