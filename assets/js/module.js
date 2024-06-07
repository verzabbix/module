class WMChatView {

	/**
	 * CSRF token for submitting new messages.
	 *
	 * @type {string}
	 */
	csrf_token_submit;

	/**
	 * Container of chat messages.
	 *
	 * @type {HTMLDivElement}
	 */
	messages_container;

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

	constructor({csrf_token_submit}) {
		this.csrf_token_submit = csrf_token_submit;

		this.messages_container = document.getElementById('wmchat-messages');

		this.startUpdatingMessages();

		const input_container = document.getElementById('wmchat-input');

		this.input = input_container.querySelector('input');
		this.input.addEventListener('keydown', e => {
			if (e.key === 'Enter') {
				this.submitMessage();
			}
		});
		this.input.focus();

		this.submit_button = input_container.querySelector('button');
		this.submit_button.addEventListener('click', () => this.submitMessage());
	}

	startUpdatingMessages() {
		const curl = new Curl('zabbix.php');
		curl.setArgument('action', 'wmchat.get');

		fetch(curl.getUrl())
			.then(response => response.json())
			.then(response => {
				if ('error' in response) {
					throw {error: response.error};
				}

		 		this.setMessages(response.chat_messages);
			})
			.catch(exception => {
				clearMessages();
				addMessage(this.makeMessageBoxFromException(exception));
			})
			.finally(() => {
				setTimeout(() => this.startUpdatingMessages(), 1000);
			});
	}

	setMessages(messages) {
		const is_max_scrolled = this.messages_container.scrollTop
			=== this.messages_container.scrollHeight - this.messages_container.clientHeight;

		this.messages_container.innerHTML = '';

		messages.forEach(message => this.appendMessage(message));

		if (is_max_scrolled) {
			this.messages_container.scrollTop =
				this.messages_container.scrollHeight - this.messages_container.clientHeight;
		}
	}

	appendMessage({author, message, time}) {
		const container_time = document.createElement('div');
		container_time.classList.add('time');
		container_time.textContent = time;

		const container_author = document.createElement('span');
		container_author.classList.add('author');
		container_author.textContent = author;

		const container_message = document.createElement('span');
		container_message.classList.add('message');
		container_message.textContent = message;

		const container_author_message = document.createElement('div');
		container_author_message.appendChild(container_author);
		container_author_message.appendChild(container_message);

		this.messages_container.appendChild(container_time);
		this.messages_container.appendChild(container_author_message);
	}

	submitMessage() {
		clearMessages();

		const message = this.input.value.trim();

		if (message === '') {
			return;
		}

		this.input.disabled = true;
		this.submit_button.disabled = true;

		const curl = new Curl('zabbix.php');
		curl.setArgument('action', 'wmchat.submit');

		fetch(curl.getUrl(), {
			method: 'POST',
			headers: {'Content-Type': 'application/json'},
			body: JSON.stringify({
				_csrf_token: this.csrf_token_submit,
				message
			})
		})
			.then(response => response.json())
			.then(response => {
				if ('error' in response) {
					throw {error: response.error};
				}

				this.input.value = '';
			})
			.catch(exception => {
				addMessage(this.makeMessageBoxFromException(exception));
			})
			.finally(() => {
				this.input.disabled = false;
				this.submit_button.disabled = false;

				this.input.focus();
			});
	}

	makeMessageBoxFromException(exception) {
		let title;
		let messages = [];

		if (typeof exception === 'object' && 'error' in exception) {
			title = exception.error.title;
			messages = exception.error.messages;
		}
		else {
			title = 'Unknown web server error';
		}

		return makeMessageBox('bad', messages, title);
	}
}
