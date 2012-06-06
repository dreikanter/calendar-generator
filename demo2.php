<h1><a href="" id="c02"></a>HTML Calendar demo #2</h1>

<?php

$c = new Calendar();

$c->addDateLink(1, 10, 2005, "http://example.net/");
$c->addDateLink(1, 10, 2005, "http://example.com/");
$c->addDateLink(10, 10, 2005, "http://example.org/");

$c->addDateTitle(2, 10, 2005, "Sed ut perspiciatis unde omnis");
$c->addDateTitle(4, 10, 2005, "Lorem ipsum dolor sit amet");
$c->addDateTitle(10, 10, 2005, "At vero eos et accusamus et iusto odio");

echo $c->genMonth(false, 10, 2005);

?>