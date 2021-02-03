<?php
$startYear = 2019;
$headerPar = $CNT->GetCurChild(0, 'header', 0);
$headerPar['userName'] = ($USER->registered)?$USER->name:0;
$headerPar['userId'] = $USER->id;
$headerPar['curYear'] = (date("Y",time())!=$startYear)?$startYear.'&nbsp;&mdash; '.date("Y",time()).'&nbsp;г.г.':$startYear.'&nbsp;г.';
$templates[] = 'default/header.tpl';

?>