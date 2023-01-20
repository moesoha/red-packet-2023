<?php

namespace SohaJin\RedPacket2023\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use SohaJin\RedPacket2023\Repository\UserRepository;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface {
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: Types::INTEGER)]
	private int $id;

	#[ORM\Column(type: Types::STRING, length: 20, unique: true)]
	#[Assert\Length(min: 4, max: 20)]
	private string $username;

	#[ORM\Column(type: Types::STRING)]
	private string $password;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
	private \DateTimeImmutable $createTime;

	#[ORM\Column(type: Types::STRING, length: 20)]
	private ?string $vpnPassword = null;

	#[ORM\OneToMany(targetEntity: VpnApplication::class, mappedBy: 'user')]
	#[ORM\OrderBy(['submitTime' => 'DESC'])]
	private array|Collection|ArrayCollection $vpnApplications;

	public function __construct() {
		$this->createTime = new \DateTimeImmutable();
	}

	public function getRoles(): array {
		return ['ROLE_USER'];
	}

	public function eraseCredentials() {}

	public function getUserIdentifier(): string {
		return $this->getUsername();
	}

	public function isVpnEnabled(): bool {
		return !empty($this->vpnPassword);
	}

	public function getLastApplication(): ?VpnApplication {
		return $this->vpnApplications->matching(Criteria::create()
			->orderBy(['submitTime' => 'DESC'])
			->setMaxResults(1)
		)->get(0);
	}

	public function getId(): int {
		return $this->id;
	}

	public function getUsername(): string {
		return $this->username;
	}

	public function setUsername(string $username): self {
		$this->username = $username;
		return $this;
	}

	public function getPassword(): string {
		return $this->password;
	}

	public function setPassword(string $password): self {
		$this->password = $password;
		return $this;
	}

	public function getCreateTime(): \DateTimeImmutable {
		return $this->createTime;
	}

	public function getVpnPassword(): ?string {
		return $this->vpnPassword;
	}

	public function generateVpnPassword(): string {
		$this->vpnPassword = bin2hex(openssl_random_pseudo_bytes(8));
		return $this->vpnPassword;
	}
}
