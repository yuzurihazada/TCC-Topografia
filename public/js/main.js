// JS básico do site
// Ajusta pequenos detalhes visuais que dependem de classes dinâmicas.

document.addEventListener('DOMContentLoaded', () => {
	document.querySelectorAll('.faq-qa').forEach((item) => {
		const collapseEl = item.querySelector('.faq-collapse');
		if (!collapseEl) return;

		const setState = (isOpen) => {
			item.classList.toggle('is-open', isOpen);
		};

		collapseEl.addEventListener('show.bs.collapse', () => setState(true));
		collapseEl.addEventListener('hide.bs.collapse', () => setState(false));

		if (collapseEl.classList.contains('show')) {
			setState(true);
		}
	});
});
