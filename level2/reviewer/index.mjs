import puppeteer from 'puppeteer';

const Headers = {
	'x-hb2023-reviewer': '1'
};
const url = path => `http://[::1]:8082${path}`;

const pendingIds = new Promise(async (resolve, reject) => {
	const browser = await puppeteer.launch();
	try {
		const page = await browser.newPage();
		await page.setExtraHTTPHeaders({ ...Headers });
		let responseResolver;
		const responseResolved = new Promise(r => { responseResolver = r; });
		page.on('response', async response => {
			if(response.status() !== 200) {
				throw new Error(await response.text());
			}
			resolve(JSON.parse(await response.text()));
			responseResolver();
		})
		await page.goto(url('/vpn/review/pending'));
		await responseResolved;
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
	page.on('dialog', async dialog => {
		await dialog.dismiss();
	});
	await page.setExtraHTTPHeaders({ ...Headers });
	for(const id of await pendingIds) {
		console.log(`handling application #${id}`);
		try {
			await page.goto(url(`/vpn/application/${id}`));
			console.log(`[${id}] wait 1s for user XSS code`);
			await page.waitForNavigation({timeout: 1000});
		} catch(e) {
			if (e.name === 'TimeoutError') {
				console.log(`[${id}] caught TimeoutError, sending reject request`);
				fetch(url('/vpn/review'), {
					method: 'post',
					body: `action=reject&id=${id}`,
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded',
						...Headers
					}
				}).then(() => console.log(`[${id}] rejected`));
				continue;
			}
		}
		console.log(`[${id}] navigation detected, no reject action`);
	}
	await browser.close();
})();
