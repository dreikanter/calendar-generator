<h1><a href="" id="c05"></a>HTML Calendar demo #5</h1>

<?php

$c = new Calendar();
$c->setLang('ru');
$c->setOptions(CLD_MONDAYS | CLD_MARKSATSUN);
$c->setCssPrefix('blank_');
echo $c->genUYear(time(), true);

?>