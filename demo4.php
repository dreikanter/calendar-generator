<h1><a href="" id="c04"></a>HTML Calendar demo #4</h1>

<?php

$c = new Calendar();

$currentMonth = time();
$prevMonth = strtotime("-1 month");
$nextMonth = strtotime("+1 month");

echo '<table border="0" cellpadding="0" cellspacing="10"><tr><td style="padding-right:4em;">';

$c->setCssPrefix('red_');
echo $c->genUMonth($prevMonth);

echo '</td><td style="padding-right:4em;">';

$c->setCssPrefix('blank_');
echo $c->genUMonth($currentMonth, true);

echo '</td><td>';

$c->setCssPrefix('simple_');
echo $c->genUMonth($nextMonth);

echo '</td></tr></table>';

?>