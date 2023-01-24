<?php declare(strict_types=1);

namespace SohaJin\RedPacket2023\Twig;

use SohaJin\RedPacket2023\DataClass\News;
use SohaJin\RedPacket2023\EventListener\IpCheckerAndLogger;
use SohaJin\RedPacket2023\Repository\NewsRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class NewsExtension extends AbstractExtension {
	public function __construct(
		private readonly RequestStack $requestStack,
		private readonly NewsRepository $newsRepository
	) {}

	public function getFunctions(): array {
		return [
			new TwigFunction('getLatestNews', fn() => $this->newsRepository->findLatest()),
			new TwigFunction('getRequestLevel', [$this, 'getRequestLevel'])
		];
	}

	public function getFilters(): array {
		return [
			new TwigFilter('levelReached', [$this, 'checkLevelReached'])
		];
	}

	public function getRequestLevel(): int {
		if(!($request = $this->requestStack->getMainRequest())) return 0;
		if($request->attributes->get(IpCheckerAndLogger::REQUEST_IN_OFFICE, false)) {
			return 3;
		} else if($request->attributes->get(IpCheckerAndLogger::REQUEST_IN_INTERNAL, false)) {
			return 2;
		}
		return 0;
	}

	public function checkLevelReached(News $news): bool {
		return $this->getRequestLevel() >= $news->getAssociatedLevel();
	}
}
