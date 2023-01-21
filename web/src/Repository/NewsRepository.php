<?php declare(strict_types=1);

namespace SohaJin\RedPacket2023\Repository;

use SohaJin\RedPacket2023\DataClass\News;

class NewsRepository {
	private static $objects = [];
	private const DATA_ID_SHIFT = 114514;
	private const DATA = [
		[self::DATA_ID_SHIFT + 0, '财务处2022年底采购相关安排', ''],
		[self::DATA_ID_SHIFT + 1, '财务处2022结账及2023开账安排', ''],
		[self::DATA_ID_SHIFT + 2, '2023年领导工作报告', '', 3],
		[self::DATA_ID_SHIFT + 3, '2023年第一季度课题申报通知', ''],
		[self::DATA_ID_SHIFT + 4, '2023年“温暖送学生”活动的通知', '', 2],
		[self::DATA_ID_SHIFT + 5, '关于2023年春节期间留校教职工的福利安排', '', 3],
	];

	public function __construct() {
		if(empty(self::$objects)) {
			self::$objects = array_map(fn($args) => new News(...$args), self::DATA);
		}
	}

	public function find(int $id): ?News {
		if($id < self::DATA_ID_SHIFT) return null;
		return self::$objects[$id - self::DATA_ID_SHIFT] ?? null;
	}

	public function findLatest(): array {
		return array_reverse(self::$objects);
	}
}
