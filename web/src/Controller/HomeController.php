<?php

namespace SohaJin\RedPacket2023\Controller;

use Doctrine\Persistence\ManagerRegistry;
use PhpIP\IP;
use SohaJin\RedPacket2023\Entity\User;
use SohaJin\RedPacket2023\EventListener\IpCheckerAndLogger;
use SohaJin\RedPacket2023\Repository\NewsRepository;
use SohaJin\RedPacket2023\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class HomeController extends AbstractController {
	#[Route('/', name: 'home')]
	public function homeAction(): Response {
		return $this->render('home.html.twig');
	}

	#[Route('/login', name: 'auth.login')]
	public function loginAction(
		TranslatorInterface $translator,
		AuthenticationUtils $authenticationUtils
	): Response {
		if($error = $authenticationUtils->getLastAuthenticationError()) {
			$errorMessage = $translator->trans($error->getMessageKey(), $error->getMessageData(), 'security');
		}
		return $this->render('login.html.twig', [
			'error' => $errorMessage ?? '',
			'username' => $authenticationUtils->getLastUsername()
		]);
	}

	#[Route('/zizhureg', name: 'auth.register')]
	public function registerAction(
		Request $request,
		UserRepository $userRepository,
		ManagerRegistry $doctrine,
		UserPasswordHasherInterface $passwordHasher
	): Response {
		$errors = [];
		if($request->isMethod(Request::METHOD_POST)) {
			$role = $request->request->get('role', 'student');
			$username = $request->request->get('username', '');
			$usernameLen = mb_strlen($username);
			$password = $request->request->get('password', '');
			$xgh = $request->request->get('xgh', '');
			if($role !== 'keren') {
				if(empty($xgh)) {
					$errors['xgh'][] = '学/工号不能为空';
				} else if(!is_numeric($xgh)) {
					$errors['xgh'][] = '学/工号只能为数字';
				} else {
					$errors['xgh'][] = '学/工号不存在';
				}
			}
			if(empty($username)) {
				$errors['username'][] = '用户名不能为空';
			} else if($usernameLen < 4 || 20 < $usernameLen) {
				$errors['username'][] = '用户名长度不符合要求';
			} else if(preg_match('/^[0-9]/', $username)) {
				$errors['username'][] = '用户名不能以数字开头';
			} else if(preg_match('/[~`!@#$%^&*()\-=+{}[\]|\\\:;"\'<>,.?\/]/', $username)) {
				$errors['username'][] = '用户名不能包含符号';
			} else if($userRepository->findOneByUsername($username)) {
				$errors['username'][] = '用户名已存在';
			}
			if(empty($errors)) {
				$session = $request->getSession();
				$user = (new User())->setUsername($username);
				$user->setPassword($passwordHasher->hashPassword($user, $password));
				if(!empty($time = $session->get(IpCheckerAndLogger::SESSION_FIRST_ACCESS_TIME, 0))) {
					$user->setFirstAccessTime((new \DateTimeImmutable())->setTimestamp((int)$time));
				}
				if($clientIp = $request->getClientIp()) {
					try {
						$user->setCreateIp(IP::create($clientIp));
					} catch(\InvalidArgumentException) {}
				}
				$doctrine->getManager()->persist($user);
				$doctrine->getManager()->flush();
				$this->addFlash('isRegistered', '1');
				return $this->redirectToRoute('auth.login');
			}
		}
		return $this->render('register.html.twig', [
			'errors' => $errors,
			'username' => $username ?? '',
			'xgh' => $xgh ?? ''
		]);
	}

	#[Route('/news/{id}', name: 'news.show')]
	public function newsAction(int $id, NewsRepository $newsRepository): Response {
		if(!($news = $newsRepository->find($id))) throw new NotFoundHttpException();
		return $this->render('news.html.twig', [
			'news' => $news
		]);
	}

	#[Route('/kaoqin', name: 'attendance')]
	public function attendanceAction(): never {
		throw new AccessDeniedHttpException();
	}

	#[Route('/course', name: 'course')]
	public function courseAction(): never {
		throw new AccessDeniedHttpException();
	}

	#[Route('/project', name: 'project')]
	public function projectAction(): never {
		throw new AccessDeniedHttpException();
	}

	#[Route('/caiwu', name: 'finance')]
	public function financeAction(): never {
		throw new AccessDeniedHttpException();
	}
}
