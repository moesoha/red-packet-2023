import puppeteer from 'puppeteer';

const url = path => `https://[::1]:8082${path}`;

const pendingIds = new Promise(async (resolve, reject) => {
	const browser = await puppeteer.launch();
	try {
		const page = await browser.newPage();
		await page.setExtraHTTPHeaders({
			'x-rp2023-reviewer': '1'
		});
		page.on('response', async response => {
			if(response.status() !== 200) {
				throw new Error(await response.text());
			}
			resolve(JSON.parse(await response.text()));
		})
		await page.goto(url('/vpn/review/pending'));
	} catch(e) {
		reject(e);
	} finally {
		await browser.close();
	}
});

(async () => {
	const browser = await puppeteer.launch();
	const page = await browser.newPage();
	page.setDefaultTimeout(2000);
	page.setDefaultNavigationTimeout(2000);
	await page.setExtraHTTPHeaders({
		'x-rp2023-reviewer': '1'
	});
	for(const id of await pendingIds) {
		console.log(`handling application #${id}`)
		await page.goto(url(`/vpn/application/${id}`));
		console.log(`[${id}] wait 1s for user XSS code`);
		try {
			await page.waitForNavigation({ timeout: 1000 });
		} catch(e) {
			if(e.name === 'TimeoutError') {
				console.log(`[${id}] triggering reject`);
				await Promise.allSettled([
					page.waitForNavigation(),
					page.click('#frm-review > #reject-it')
				]);
				console.debug(`[${id}] rejected`);
				continue;
			}
		}
		console.log(`[${id}] navigation detected, no reject action`);
	}
	await browser.close();
})();
