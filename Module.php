<?php

namespace Modules\WMChat;

use APP,
	CMenuItem,
	Zabbix\Core\CModule;

class Module extends CModule {

	public const MACRO_HOST_NAME = '{$WMCHAT_HOST_NAME}';
	public const MACRO_ITEM_KEY = '{$WMCHAT_ITEM_KEY}';

	public function init(): void {
		APP::Component()->get('menu.main')->add(
			(new CMenuItem('Wellmade chat'))
				->setAction('wmchat.view')
				->setIcon(ZBX_ICON_ENVELOPE_FILLED)
		);
	}
}
