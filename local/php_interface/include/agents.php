<?php

function CheckUserCount()
{

    $curDate = date('Y-m-d H:i:s');
    $lastActivation = COption::GetOptionString('main', 'lastActivateAgentUserCount');
    $filter = [];
    
    if ($lastActivation) {
        $filter = [
            'DATE_REGISTER_1' => $lastActivation,
        ];
    }

    $userList = [];

    $rsUsers = CUser::GetList(false, '', $filter);
    $userCount = $rsUsers->SelectedRowsCount();

    while ($user = $rsUsers->GetNext()) {
        $userList[] = $user;
    }

    if (!$lastActivation) {
        $lastActivation = $userList[0]['DATE_REGISTER'];
    }

    $secondPeriod = intval(strtotime($curDate) - strtotime($lastActivation));
    $dayPeriod = round($secondPeriod / 86400);

    $rsAdmins = CUser::GetList(false, '', ['GROUPS_ID' => 1]);
    while ($admin = $rsAdmins->GetNext()) {
        CEvent::Send(
            'EVENT_TASK_EXAM_2',
            's1',
            [
                'EMAIL_TO' => $admin['EMAIL'],
                'COUNT' => $userCount,
                'DAYS' => $dayPeriod,
            ],
            'N',
            '33'
        );
    }

    COption::SetOptionString('main', 'lastActivateAgentUserCount', $curDate);
    
    return "CheckUserCount();";
}