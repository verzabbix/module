<?php

namespace Modules\WMChat\Includes;

use API,
	Modules\WMChat\Module;

class WMChatHelper {

	public static function getItemId(): string {
		$macros = API::UserMacro()->get([
			'output' => ['macro', 'value'],
			'globalmacro' => true,
			'search' => [
				'macro' => [Module::MACRO_HOST_NAME, Module::MACRO_ITEM_KEY]
			],
			'searchByAny' => true
		]);

		$macros = array_column($macros, 'value', 'macro');

		$items = API::Item()->get([
			'output' => ['itemid'],
			'filter' => [
				'host' => $macros[Module::MACRO_HOST_NAME],
				'key_' => $macros[Module::MACRO_ITEM_KEY]
			]
		]);

		return $items[0]['itemid'];
	}
}
