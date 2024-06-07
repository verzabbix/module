class WMChatView {

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

	constructor() {
		this.messages_container = document.getElementById('wmchat-messages');

		this.startUpdatingMessages();

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

	startUpdatingMessages() {
		const curl = new Curl('zabbix.php');
		curl.setArgument('action', 'wmchat.get');

		fetch(curl.getUrl())
			.then(response => response.json())
			.then(response => {
		 		this.setMessages(response.chat_messages);
			})
			.finally(() => {
				setTimeout(() => this.startUpdatingMessages(), 1000);
			});
	}

	setMessages(messages) {
		this.messages_container.innerHTML = '';

		messages.forEach(message => this.appendMessage(message));
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
