<?php
if ($_REQUEST['act'] == 'gen_excel')
{
    @set_time_limit(0);

    /* 获得此线下红包类型的ID */
    $tid  = !empty($_GET['tid']) ? intval($_GET['tid']) : 0;
    $type_name = $db->getOne("SELECT type_name FROM ".$ecs->table('bonus_type')." WHERE type_id = '$tid'");

    /* 文件名称 */
    $bonus_filename = $type_name .'_bonus_list';
    if (EC_CHARSET != 'gbk')
    {
        $bonus_filename = ecs_iconv('UTF8', 'GB2312',$bonus_filename);
    }

    header("Content-type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=$bonus_filename.xls");

    /* 文件标题 */
    if (EC_CHARSET != 'gbk')
    {
        echo ecs_iconv('UTF8', 'GB2312', $_LANG['bonus_excel_file']) . "\t\n";
        /* 红包序列号, 红包金额, 类型名称(红包名称), 使用结束日期 */
        echo ecs_iconv('UTF8', 'GB2312', $_LANG['bonus_sn']) ."\t";
        echo ecs_iconv('UTF8', 'GB2312', $_LANG['type_money']) ."\t";
        echo ecs_iconv('UTF8', 'GB2312', $_LANG['type_name']) ."\t";
        echo ecs_iconv('UTF8', 'GB2312', $_LANG['use_enddate']) ."\t\n";
    }
    else
    {
        echo $_LANG['bonus_excel_file'] . "\t\n";
        /* 红包序列号, 红包金额, 类型名称(红包名称), 使用结束日期 */
        echo $_LANG['bonus_sn'] ."\t";
        echo $_LANG['type_money'] ."\t";
        echo $_LANG['type_name'] ."\t";
        echo $_LANG['use_enddate'] ."\t\n";
    }

    $val = array();
    $sql = "SELECT ub.bonus_id, ub.bonus_type_id, ub.bonus_sn, bt.type_name, bt.type_money, bt.use_end_date ".
           "FROM ".$ecs->table('user_bonus')." AS ub, ".$ecs->table('bonus_type')." AS bt ".
           "WHERE bt.type_id = ub.bonus_type_id AND ub.bonus_type_id = '$tid' ORDER BY ub.bonus_id DESC";
    $res = $db->query($sql);

    $code_table = array();
    while ($val = $db->fetchRow($res))
    {
        echo $val['bonus_sn'] . "\t";
        echo $val['type_money'] . "\t";
        if (!isset($code_table[$val['type_name']]))
        {
            if (EC_CHARSET != 'gbk')
            {
                $code_table[$val['type_name']] = ecs_iconv('UTF8', 'GB2312', $val['type_name']);
            }
            else
            {
                $code_table[$val['type_name']] = $val['type_name'];
            }
        }
        echo $code_table[$val['type_name']] . "\t";
        echo local_date('Y-m-d', $val['use_end_date']);
        echo "\t\n";
    }
}