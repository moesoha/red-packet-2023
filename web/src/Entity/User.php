<?php

namespace SohaJin\RedPacket2023\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PhpIP\IP;
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

	#[ORM\Column(type: 'inet', nullable: true)]
	private ?IP $createIp = null;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
	private ?\DateTimeImmutable $firstAccessTime = null;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
	private ?\DateTimeImmutable $internalAccessTime = null;

	#[ORM\Column(type: 'inet', nullable: true)]
	private ?IP $internalAccessIp = null;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
	private ?\DateTimeImmutable $officeAccessTime = null;

	#[ORM\Column(type: 'inet', nullable: true)]
	private ?IP $officeAccessIp = null;

	#[ORM\Column(type: Types::STRING, length: 20, nullable: true)]
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

	public function getFirstAccessTime(): ?\DateTimeImmutable {
		return $this->firstAccessTime;
	}

	public function setFirstAccessTime(?\DateTimeImmutable $firstAccessTime): self {
		$this->firstAccessTime = $firstAccessTime;
		return $this;
	}

	public function getInternalAccessTime(): ?\DateTimeImmutable {
		return $this->internalAccessTime;
	}

	public function setInternalAccessTime(?\DateTimeImmutable $internalAccessTime): self {
		$this->internalAccessTime = $internalAccessTime;
		return $this;
	}

	public function getOfficeAccessTime(): ?\DateTimeImmutable {
		return $this->officeAccessTime;
	}

	public function setOfficeAccessTime(?\DateTimeImmutable $officeAccessTime): self {
		$this->officeAccessTime = $officeAccessTime;
		return $this;
	}

	public function getVpnPassword(): ?string {
		return $this->vpnPassword;
	}

	public function generateVpnPassword(): string {
		$this->vpnPassword = bin2hex(openssl_random_pseudo_bytes(8));
		return $this->vpnPassword;
	}

	public function getCreateIp(): ?IP {
		return $this->createIp;
	}

	public function setCreateIp(?IP $createIp): self {
		$this->createIp = $createIp;
		return $this;
	}

	public function getInternalAccessIp(): ?IP {
		return $this->internalAccessIp;
	}

	public function setInternalAccessIp(?IP $internalAccessIp): self {
		$this->internalAccessIp = $internalAccessIp;
		return $this;
	}

	public function getOfficeAccessIp(): ?IP {
		return $this->officeAccessIp;
	}

	public function setOfficeAccessIp(?IP $officeAccessIp): self {
		$this->officeAccessIp = $officeAccessIp;
		return $this;
	}
}
