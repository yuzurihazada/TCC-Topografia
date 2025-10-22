// JS básico do site
// Ajusta pequenos detalhes visuais que dependem de classes dinâmicas.

document.addEventListener('DOMContentLoaded', () => {
	// Ajusta o acordeão de FAQ para aplicar classe auxiliar quando estiver aberto.
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

	// Constrói o link dinâmico do WhatsApp com base nos campos preenchidos.
	const waTrigger = document.querySelector('[data-wa-button]');
	if (waTrigger) {
		const form = document.querySelector('.contact-form');
		const waNumber = waTrigger.getAttribute('data-wa-number') || '';
		const serviceTopic = waTrigger.getAttribute('data-service-topic') || '';

		waTrigger.addEventListener('click', (event) => {
			if (!waNumber) {
				return;
			}

			// Impede o redirecionamento caso o formulário ainda tenha erros.
			if (form && !form.checkValidity()) {
				event.preventDefault();
				form.reportValidity();
				return;
			}

			event.preventDefault();

			// Captura os valores atuais do formulário para montar a mensagem.
			const getValue = (selector) => {
				const input = form ? form.querySelector(selector) : null;
				return input ? input.value.trim() : '';
			};

			const firstName = getValue('[name="first_name"]');
			const lastName = getValue('[name="last_name"]');
			const phone = getValue('[name="phone"]');
			const email = getValue('[name="email"]');
			const message = getValue('[name="message"]').replace(/\r\n/g, '\n');

			// Monta o corpo da mensagem seguindo o padrão solicitado.
			const lines = [];
			if (serviceTopic) {
				lines.push(`Olá, gostaria de um orçamento para o serviço ${serviceTopic}.`);
			} else {
				lines.push('Olá,');
			}
			lines.push('');

			const fullName = `${firstName} ${lastName}`.trim();
			lines.push(`Nome: ${fullName || '-'}`);
			lines.push(`Telefone: ${phone || '-'}`);
			lines.push(`E-mail: ${email || '-'}`);
			const messageLine = message || '-';
			lines.push(`Mensagem: ${messageLine}`);

			// Abre o WhatsApp com o texto preenchido, preservando a aba original.
			const waText = encodeURIComponent(lines.join('\n'));
			const waUrl = `https://api.whatsapp.com/send?phone=${waNumber}&text=${waText}`;
			window.open(waUrl, '_blank', 'noopener');
		});
	}
});
