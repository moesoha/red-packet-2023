<?php declare(strict_types=1);

namespace SohaJin\RedPacket2023\Twig;

use SohaJin\RedPacket2023\Repository\NewsRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NewsExtension extends AbstractExtension {
	public function __construct(
		private readonly NewsRepository $newsRepository
	) {}

	public function getFunctions(): array {
		return [
			new TwigFunction('getLatestNews', fn() => $this->newsRepository->findLatest())
		];
	}
}
