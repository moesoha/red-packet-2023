<?php declare(strict_types=1);

namespace SohaJin\RedPacket2023\DataClass;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class Reviewer implements UserInterface, UserProviderInterface {
	public const UserIdentifier = '#reviewer#';

	public function getUserIdentifier(): string {
		return self::UserIdentifier;
	}

	public function getRoles(): array {
		return ['ROLE_REVIEWER'];
	}

	public function eraseCredentials() {}

	public function refreshUser(UserInterface $user): UserInterface {
		if($user instanceof Reviewer) {
			return new self();
		}
		throw new UnsupportedUserException();
	}

	public function supportsClass(string $class): string {
		return self::class;
	}

	public function loadUserByIdentifier(string $identifier): UserInterface {
		if($identifier !== '#reviewer#') {
			throw new UserNotFoundException();
		}
		return new self();
	}
}
