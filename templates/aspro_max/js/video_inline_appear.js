BX.addCustomEvent('onAppearVideoBlockShow', (eventdata) => {
	if (!eventdata.target.classList.contains('video_block')) return;
	
	const videoList = eventdata.target.querySelectorAll('iframe, video');
	videoList.forEach((node) => {
		if (node.dataset.src) {
			node.src = node.dataset.src;
			node.removeAttribute('data-src');
		}

		if (node.classList.contains('video-js')) {
			node.addEventListener('canplay', function() {
				node.classList.remove('hidden');
			});
		}
	});
});