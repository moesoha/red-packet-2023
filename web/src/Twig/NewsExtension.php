<?php declare(strict_types=1);

namespace SohaJin\RedPacket2023\Twig;

use PhpIP\IP;
use PhpIP\IPBlock;
use SohaJin\RedPacket2023\DataClass\News;
use SohaJin\RedPacket2023\Repository\NewsRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class NewsExtension extends AbstractExtension {
	public function __construct(
		#[Autowire('@cidr.internal')] private readonly IPBlock $cidrInternal,
		#[Autowire('@cidr.office')] private readonly IPBlock $cidrOffice,
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
		if(!($request = $this->requestStack->getCurrentRequest()) || empty($ipString = $request->getClientIp())) {
			return false;
		}
		try {
			$ip = IP::create($ipString);
		} catch(\InvalidArgumentException) {
			return false;
		}
		$level = 0;
		if($this->cidrOffice->containsIP($ip)) {
			$level = 3;
		} else if($this->cidrInternal->containsIP($ip)) {
			$level = 2;
		}
		return $level >= $news->getAssociatedLevel();
	}
}
