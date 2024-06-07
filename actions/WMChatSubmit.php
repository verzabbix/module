<?php

namespace Modules\WMChat\Actions;

use API,
	CController,
	CControllerResponseData,
	CWebUser;

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
		$author = CWebUser::$data['name'] !== '' || CWebUser::$data['surname'] !== ''
			? CWebUser::$data['name'].' '.CWebUser::$data['surname']
			: CWebUser::$data['username'];

		$value = json_encode([
			'author' => $author,
			'message' => $this->getInput('message')
		]);

		API::History()->push([
			'itemid' => '47201',
			'value' => $value
		]);

		$output = [];

		$this->setResponse(new CControllerResponseData(['main_block' => json_encode($output)]));
	}
}
