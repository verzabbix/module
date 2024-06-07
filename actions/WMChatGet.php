<?php

namespace Modules\WMChat\Actions;

use API,
	CController,
	CControllerResponseData,
	Modules\WMChat\Includes\WMChatHelper,
	RuntimeException;

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
		$itemid = null;
		$error_messages = [];

		try {
			$itemid = WMChatHelper::getItemId();
		}
		catch (RuntimeException $e) {
			$error_messages = [$e->getMessage()];
		}

		if (!$error_messages) {
			$history_data = API::History()->get([
				'output' => ['clock', 'value'],
				'history' => ITEM_VALUE_TYPE_STR,
				'itemids' => $itemid,
				'sortfield' => 'clock',
				'sortorder' => 'ASC'
			]);

			if ($history_data === false) {
				$error_messages = array_column(get_and_clear_messages(), 'message');
			}
		}

		$output = [];

		if ($error_messages) {
			$output['error'] = [
				'title' => 'Failed to fetch messages',
				'messages' => $error_messages
			];
		}
		else {
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
		}

		$this->setResponse(new CControllerResponseData(['main_block' => json_encode($output)]));
	}
}
