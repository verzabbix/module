<?php

/**
 * @var CView $this
 * @var array $data
 */

(new CHtmlPage())
	->setTitle('Wellmade chat')
	->addItem(
		(new CDiv())
			->setId('wmchat-container')
			->addItem([
				(new CDiv())->setId('wmchat-messages'),
				(new CDiv())
					->setId('wmchat-input')
					->addItem(
						(new CTextBox('message'))->setAttribute('autocomplete', 'off')
					)
					->addItem(new CSimpleButton('Send'))
			])
	)
	->show();

(new CScriptTag('
	new WMChatView('.json_encode([
		'csrf_token_submit' => CCsrfTokenHelper::get('wmchat.submit')
	]).');
'))
	->setOnDocumentReady()
	->show();
