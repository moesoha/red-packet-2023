<?php declare(strict_types=1);

namespace SohaJin\RedPacket2023\DataClass;

readonly class News {
	public function __construct(
		private int $id,
		private string $title,
		private string $content,
		private int $associatedLevel = 0,
		private bool $showCode = false
	) {}

	public function getId(): int {
		return $this->id;
	}

	public function getTitle(): string {
		return $this->title;
	}

	public function getContent(): string {
		return $this->content;
	}

	public function getAssociatedLevel(): int {
		return $this->associatedLevel;
	}

	public function isShowCode(): bool {
		return ($this->associatedLevel > 0) && $this->showCode;
	}
}
