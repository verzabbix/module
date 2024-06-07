<?php

namespace Modules\WMChat\Actions;

use API,
	CController,
	CControllerResponseData,
	Modules\WMChat\Includes\WMChatHelper;

class WMChatGet extends CController {

	public function init(): void {
		$this->disableCsrfValidation();
	}

	protected function checkInput(): bool {
		return true;
	}

	protected function checkPermissions(): bool {
		return true;
	}

	protected function doAction(): void {
		$itemid = WMChatHelper::getItemId();

		$history_data = API::History()->get([
			'output' => ['clock', 'value'],
			'history' => ITEM_VALUE_TYPE_STR,
			'itemids' => $itemid,
			'sortfield' => 'clock',
			'sortorder' => 'ASC'
		]);

		$output = [];

		$output['chat_messages'] = array_map(
			static function (array $history_entry): array {
				[
					'author' => $author,
					'message' => $message
				] = json_decode($history_entry['value'], true);

				return [
					'author' => $author,
					'message' => $message,
					'time' => date(ZBX_FULL_DATE_TIME, $history_entry['clock'])
				];
			},
			$history_data
		);

		$this->setResponse(new CControllerResponseData(['main_block' => json_encode($output)]));
	}
}
