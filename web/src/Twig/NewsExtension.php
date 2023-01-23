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
			new TwigFunction('getLatestNews', fn() => $this->newsRepository->findLatest())
		];
	}

	public function getFilters(): array {
		return [
			new TwigFilter('levelReached', [$this, 'checkLevelReached'])
		];
	}

	public function checkLevelReached(News $news): bool {
		if(!($request = $this->requestStack->getMainRequest())) return false;
		$level = 0;
		if($request->attributes->get(IpCheckerAndLogger::REQUEST_IN_OFFICE, false)) {
			$level = 3;
		} else if($request->attributes->get(IpCheckerAndLogger::REQUEST_IN_INTERNAL, false)) {
			$level = 2;
		}
		return $level >= $news->getAssociatedLevel();
	}
}
