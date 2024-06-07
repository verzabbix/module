class WMChatView {

	/**
	 * New message text input.
	 *
	 * @type {HTMLInputElement}
	 */
	input;

	/**
	 * New message submit button.
	 *
	 * @type {HTMLButtonElement}
	 */
	submit_button;

	constructor() {
		const input_container = document.getElementById('wmchat-input');

		this.input = input_container.querySelector('input');
		this.input.addEventListener('keydown', e => {
			if (e.key === 'Enter') {
				this.submitMessage();
			}
		});

		this.submit_button = input_container.querySelector('button');
		this.submit_button.addEventListener('click', () => this.submitMessage());
	}

	submitMessage() {
		const message = this.input.value.trim();

		if (message === '') {
			return;
		}

		const curl = new Curl('zabbix.php');
		curl.setArgument('action', 'wmchat.submit');

		fetch(curl.getUrl(), {
			method: 'POST',
			headers: {'Content-Type': 'application/json'},
			body: JSON.stringify({message})
		});
	}
}
