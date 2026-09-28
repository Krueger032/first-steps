function setCookieOnDomains(name, id, config = {}) {
	const defaultConfig = { path: "/", domain: arAsproOptions["SITE_ADDRESS"] };
	config = Object.assign({}, defaultConfig, config);

	if (arAsproOptions["SITE_ADDRESS"].indexOf(",") != "-1") {
		const arDomains = arAsproOptions["SITE_ADDRESS"].split(",");
		if (arDomains) {
			for (let i in arDomains) {
				const domain_name = arDomains[i].replace("\n", "").replace("'", "");

				config = Object.assign({}, config, { domain: domain_name });

				$.cookie(name, id, config);
			}
		}
	} else {
		$.cookie(name, id, config);
	}
}
