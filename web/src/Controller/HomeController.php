<?php

namespace SohaJin\RedPacket2023\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {
	#[Route('/login', name: 'auth.login')]
	public function loginAction(): Response {
		return $this->render('login.html.twig');
	}

	#[Route('/zizhureg', name: 'auth.register')]
	public function registerAction(): Response {
		return $this->render('register.html.twig');
	}
}
