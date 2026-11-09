document.addEventListener('DOMContentLoaded', () => {
	const root = document;
	root.querySelectorAll('[data-wikipress-count]').forEach((element) => {
		element.classList.add('wikipress-count-ready');
	});
});



