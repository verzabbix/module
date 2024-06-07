<?php

namespace Modules\WMChat\Actions;

use CController,
	CControllerResponseData;

class WMChatView extends CController {

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
		$output = [];

		$this->setResponse(new CControllerResponseData($output));
	}
}
