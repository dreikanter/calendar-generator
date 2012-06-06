<h1><a href="" id="c03"></a>HTML Calendar demo #3</h1>

<?php

$c = new Calendar();
$c->setOptions(CLD_MARKSATSUN | CLD_MONDAYS);
// third parameter is the monthes count in the row
echo $c->genUYear(time(), true, 3);

?>