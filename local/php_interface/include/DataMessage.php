<?php
AddEventHandler('main', 'OnBeforeEventAdd', array('DataMessage', 'OnBeforeEventAddHandler'));

use Bitrix\Main\Engine\CurrentUser;

class DataMessage
{
	public static function OnBeforeEventAddHandler(&$event, &$lid, &$arFields)
	{
		if($event == 'FEEDBACK_FORM'){
			$curUser = CurrentUser::get();

			if($curUser->getId()){
				
				$arFields['AUTHOR'] = GetMessage('USER_AUTORIZED', [
					'#ID#' => $curUser->getID(),
					'#LOGIN#' => $curUser->getLogin(),
					'#FULL_NAME#' => $curUser->getFullName(),
					'#FORM_DATA#' => $arFields['AUTHOR']
				]);
			}
			else{
				$arFields['AUTHOR'] = GetMessage('USER_IS_NOT_AUTORIZED', ['#FORM_DATA#' => $arFields['AUTHOR']]);
			}
		}

		CEventLog::Add(array(
			'SEVERITY' => 'SECURITY',
			'AUDIT_TYPE_ID' => 'CHANGE_DATA_MESSAGE',
			'MODULE_ID' => 'main',
			'ITEM_ID' => '',
			'DESCRIPTION' => GetMessage('CHANGE_DATA_MESSAGE', ['#FORM_DATA#' => $arFields['AUTHOR']])
		));
	}
}
