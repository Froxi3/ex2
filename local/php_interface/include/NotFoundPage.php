<?php
AddEventHandler('main', 'OnEpilog', ['NotFoundPage', 'OnEpilogHandler']);

class NotFoundPage
{
    public static function OnEpilogHandler()
    {
        if (defined('ERROR_404') && ERROR_404 == 'Y') {
            global $APPLICATION;

            CEventLog::Add([
                'SEVERITY' => 'INFO',
                'AUDIT_TYPE_ID' => 'ERROR_404',
                'MODULE_ID' => 'main',
                'ITEM_ID' => '',
                'DESCRIPTION' => $APPLICATION->GetCurUri(),
            ]);
        }
    }
}
