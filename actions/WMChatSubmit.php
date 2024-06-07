<?php

namespace Modules\WMChat\Actions;

use API,
	CController,
	CControllerResponseData,
	CWebUser,
	Modules\WMChat\Includes\WMChatHelper,
	RuntimeException;

class WMChatSubmit extends CController {

	protected function init(): void {
		$this->disableCsrfValidation();
		$this->setPostContentType(self::POST_CONTENT_TYPE_JSON);
	}

	protected function checkInput(): bool {
		$fields = [
			'message' => 'required|not_empty|string'
		];

		$ret = $this->validateInput($fields);

		if (!$ret) {
			$this->setResponse(
				new CControllerResponseData(['main_block' => json_encode([
					'error' => [
						'messages' => array_column(get_and_clear_messages(), 'message')
					]
				])])
			);
		}

		return $ret;
	}

	protected function checkPermissions(): bool {
		return true;
	}

	protected function doAction(): void {
		$itemid = null;
		$error_messages = [];

		try {
			$itemid = WMChatHelper::getItemId();
		}
		catch (RuntimeException $e) {
			$error_messages = [$e->getMessage()];
		}

		if (!$error_messages) {
			$author = CWebUser::$data['name'] !== '' || CWebUser::$data['surname'] !== ''
				? CWebUser::$data['name'].' '.CWebUser::$data['surname']
				: CWebUser::$data['username'];

			$value = json_encode([
				'author' => $author,
				'message' => $this->getInput('message')
			]);

			if (strlen($value) > 255) {
				$error_messages = [sprintf('Message is %1$d characters too long.', strlen($value) - 255)];
			}
		}

		if (!$error_messages) {
			$result = API::History()->push([
			 	'itemid' => $itemid,
				'value' => $value
			]);

			if (!$result) {
				$error_messages = array_column(get_and_clear_messages(), 'message');
			}
		}

		$output = [];

		if ($error_messages) {
			$output['error'] = [
				'title' => 'Failed to submit message',
				'messages' => $error_messages
			];
		}

		$this->setResponse(new CControllerResponseData(['main_block' => json_encode($output)]));
	}
}
