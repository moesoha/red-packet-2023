<?php declare(strict_types=1);

namespace SohaJin\RedPacket2023\Security;

use PhpIP\IP;
use PhpIP\IPBlock;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReviewerIpAuthenticator extends AbstractAuthenticator {
	public function __construct(
		#[Autowire('@cidr.reviewer')] private readonly IPBlock $cidrReviewer,
		private readonly TranslatorInterface $translator
	) {}

	public function supports(Request $request): ?bool {
		return $request->headers->has('x-hb2023-reviewer');
	}

	public function authenticate(Request $request): Passport {
		if(!($ip = $request->getClientIp())) {
			throw new CustomUserMessageAuthenticationException('cannot get IP');
		}
		if(!$this->cidrReviewer->contains(IP::create($ip))) {
			throw new CustomUserMessageAuthenticationException('unauthorized IP');
		}
		return new SelfValidatingPassport(new UserBadge('#reviewer#'));
	}

	public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response {
		return null;
	}

	public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response {
		return new Response(
			$this->translator->trans($exception->getMessageKey(), $exception->getMessageData()),
			Response::HTTP_UNAUTHORIZED,
			['Content-Type' => 'text/plain']
		);
	}
}
