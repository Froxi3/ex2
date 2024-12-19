<?php
AddEventHandler('main', 'OnBuildGlobalMenu', array('MenuBuilder', 'OnBuildGlobalMenuHandler'));

use Bitrix\Main\Engine\CurrentUser;

class MenuBuilder 
{
    public static function OnBuildGlobalMenuHandler(&$aGlobalMenu, &$aModuleMenu)
    {
        $idAdminGroup = 1;
        $idContentGroup = 5;
        $userGroupList = CurrentUser::get()->getUserGroups();
        
        if (in_array($idContentGroup, $userGroupList) && !(in_array($idAdminGroup, $userGroupList))) {
            unset($aGlobalMenu['global_menu_desktop']);
            
            foreach ($aModuleMenu as $key => $value) {
                if (($value['parent_menu'] != 'global_menu_content') || ($value['section'] == 'iblock')) {
                    unset($aModuleMenu[$key]);
                }
            }
        }
    }
}
