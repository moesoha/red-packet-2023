<?php declare(strict_types=1);

namespace SohaJin\RedPacket2023\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use SohaJin\RedPacket2023\Repository\VpnApplicationRepository;

#[ORM\Entity(repositoryClass: VpnApplicationRepository::class)]
#[ORM\Index(fields: ['user', 'result'])]
#[ORM\Index(fields: ['user', 'submitTime'])]
#[ORM\Index(fields: ['result', 'submitTime'])]
class VpnApplication {
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: Types::INTEGER)]
	private int $id;

	#[ORM\ManyToOne(targetEntity: User::class), ORM\JoinColumn(nullable: false)]
	private User $user;

	#[ORM\Column(type: Types::TEXT)]
	private string $statement;

	#[ORM\Column(type: Types::BOOLEAN, nullable: true)]
	private ?bool $result = null;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
	private \DateTimeImmutable $submitTime;

	#[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
	private ?\DateTimeImmutable $reviewTime = null;

	public function __construct() {
		$this->submitTime = new \DateTimeImmutable();
	}

	public function getId(): int {
		return $this->id;
	}

	public function getUser(): User {
		return $this->user;
	}

	public function setUser(User $user): self {
		$this->user = $user;
		return $this;
	}

	public function getStatement(): string {
		return $this->statement;
	}

	public function setStatement(string $statement): self {
		$this->statement = $statement;
		return $this;
	}

	public function getResult(): ?bool {
		return $this->result;
	}

	public function setResult(?bool $result): self {
		$this->result = $result;
		$this->reviewTime = new \DateTimeImmutable();
		return $this;
	}

	public function getSubmitTime(): \DateTimeImmutable {
		return $this->submitTime;
	}

	public function getReviewTime(): \DateTimeImmutable {
		return $this->reviewTime;
	}
}
