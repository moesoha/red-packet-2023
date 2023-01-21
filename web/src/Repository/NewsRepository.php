<?php declare(strict_types=1);

namespace SohaJin\RedPacket2023\Repository;

use SohaJin\RedPacket2023\DataClass\News;

class NewsRepository {
	private static $objects = [];
	private const DATA_ID_SHIFT = 114514;
	private const DATA = [
		[self::DATA_ID_SHIFT + 0, '财务处2022年底采购相关安排', <<<EOF
<p>各学院、各部门：</p>
<p>每年 12 月份，财务处需向 foobar 院申请结转本研究所该年度未完成支付的采购项目，以便下年度进行支付。为了在申请截止时间前顺利完成该工作，同时保证年底到期经费采购项目的顺利支付，现将年底采购受理时间节点通知如下：</p>
<ol>
	<li>foobar 院采购项目申请受理截止日期为 2022 年 11 月 15 日，foobar 院采购支付申请受理截止时间为 2022 年 11 月 25 日；</li>
	<li>研究所统一采购项目需资产处负责采购，采购周期较长，11 月 7 日后申请的采购项目，如需研究所统一采购，且所用经费年底到期，请先与资产处联系，以确保能够完成支付；</li>
	<li>自行采购的项目，经费负责人应先向供应商明确供货期，确保年底前完成支付；</li>
	<li>请各位老师及时办理相关业务，以免影响工作。</li>
	<li>请各部门务必将本通知内容通知到位。</li>
</ol>
<br />
<p>有关政策解释，可咨询财务处。</p>
<br />
<p style="text-align: right">
	foobar 院新年红包研究所财务处
	<br />
	2022 年 11 月 4 日
</p>
EOF],
		[self::DATA_ID_SHIFT + 1, '财务处2022结账及2023开账安排', <<<EOF
<p>各位师生：</p>
<p>按照年终结账部署，为顺利完成 foobar 院 2022 年度预算执行、年终结账、财务决算、明年的预算下拨及开账工作，我研究所年末财务报销时间安排如下：</p>
<p style="font-size: 1.175em;">一、2022 年度接单安排</p>
<ol>
	<li>教学个人项目经费、修缮经费、基建工程款、设备购置费、初创培育费报销截止日期为 11 月 28 日（周一）。</li>
	<li>除以上项目外，2022 年度报销（含工资、酬金）截止时间为 2022 年 12 月 7 日（周三），如截止日期为 2022 年 12 月 31 日之前的项目请在 2022 年 12 月 7 日前办理报销手续。</li>
</ol>
<p style="font-size: 1.175em;">二、2023 年度接单安排</p>
<p>2023 年度的财务报销工作计划将于 2023 年 1 月 5 日（周四）开始。</p>
<p style="font-size: 1.175em;">三、其他</p>
<p>“工会、党委、教育基金会”账套 2022 年报销截止时间和 2023 年开账时间安排同上。</p>
<br />
<p>请大家提前做好年末报销安排及明年的资金支出规划，停账期间造成不便，敬请谅解，谢谢！</p>
<br />
<p style="text-align: right">
	foobar 院新年红包研究所财务处
	<br />
	2022 年 11 月 10 日
</p>
EOF],
		[self::DATA_ID_SHIFT + 2, '2022年所长办公室工作报告', <<<EOF
<p>这个研究所就是 2023 年春节红包临时成立的，哪来的工作报告。</p>
<p style="text-align: right">
	foobar 院新年红包研究所所长办公室
	<br />
	2022 年 12 月 31 日
</p>
EOF, 3],
		[self::DATA_ID_SHIFT + 3, '2023年度课题申报通知', <<<EOF
<p>各部门、各位师生：</p>
<p>为全面贯彻“面向世界科技前沿”的方针，落实发展科学技术的根本任务，现开展 foobar 院新年红包研究所 2023 年度“新年红包”课题的申报工作，相关事项通知如下：</p>
<p style="font-size: 1.175em;">一、课题类型及相关要求</p>
<p>课题需要紧密切合研究所对于新年红包的研究特点，侧重个性化实践探索。</p>
<p>研究周期为 1 年，不得延期。结题需提交《课题研究报告》。此类课题可以个人独立申报、独立实践，也可以组建研究小组，但研究课题需有深度。</p>
<p>研究过程需践行 foobar 院“逢山开路、遇水架桥，一步一步攻难关”的精神，不得中途放弃、糊弄了事。</p>
<p style="font-size: 1.175em;">二、申报方法</p>
<ol>
	<li>全所共可申报 20 项课题，最后择优批准其中 10 项。</li>
	<li>教务处需对课题申报者资格分类严格把关，所内将组建专家组，对所有提交的课题进行分析研判，并听取课题负责人的报告，认真评选最终的课题。</li>
	<li>请于 2023 年 1 月 19 日前完成申报，申报方式为发送电子申请表到评选组组长邮箱 monkey@foobar.ac.cn。</li>
</ol>
<p style="text-align: right">
	foobar 院新年红包研究所教务处
	<br />
	2023 年 1 月 5 日
</p>
EOF],
		[self::DATA_ID_SHIFT + 4, '2023年“温暖送学生”活动的通知', <<<EOF
<p>各位同学：</p>
<p>为体现出对学生的关怀，本研究所即将启动 2023 年“温暖送学生”活动，今年活动如下：</p>
<ol>
	<li>所有学生可获得研究所的新年祝福邮件。</li>
	<li>所有学生可在内网访问本页面获得支付宝红包口令。</li>
</ol>
<p>祝大家新年快乐，如有疑问可联系电子邮箱 soha@foobar.ac.cn。</p>
<p style="text-align: right">
	foobar 院新年红包研究所所长办公室
	<br />
	2023 年 1 月 16 日
</p>
EOF, 2, true],
		[self::DATA_ID_SHIFT + 5, '关于2023年春节期间留校教职工的福利安排', <<<EOF
<p>各位教职工：</p>
<p>感谢各位一年的辛苦付出，在春节这个阖家团圆的日子里还坚持留所，为研究所的建设做出自己的贡献。因此本研究所为老师们提供如下福利：</p>
<ol>
	<li>所有教职工可在办公室通过内网访问本页面获得支付宝红包口令。</li>
</ol>
<p>祝大家新年快乐，如有疑问可联系电子邮箱 soha@foobar.ac.cn。</p>
<p style="text-align: right">
	foobar 院新年红包研究所所长办公室
	<br />
	2023 年 1 月 21 日
</p>
EOF, 3, true],
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
