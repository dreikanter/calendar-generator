# PHP-класс для генерации календарей

Простой генератор HTML-календарей, написанный на PHP4 в 2006, кажется, году.

* Умеет генерировать календарь на отдельно заданный месяц или год.
* Названия месяцев и дней недели, могут отображаться на русском и английском языках. Можно добавлять другие языки.
* Первым днём недели можно задать воскресенье или понедельник.
* Может выделять заданные даты и выходные дни.
* Умеет привязывать к датам гиперссылки и добавлять текст в тултипах.
* Оформление сделано полностью через внешние таблицы стилей. В комплект поставки входит три разных типа оформления, по образу и подобию которых можно делать свои собственные.

Сгенерированный календарь может выглядеть, например, так:

![Generated calendar](/dreikanter/calendar-generator/raw/master/screenshots/2-calendar-screenshot.png)

Оформление несложно кастомизировать:

![Customized calendars](/dreikanter/calendar-generator/raw/master/screenshots/3-calendar-screenshot.png)

Комплект поставки:

* `calendar.class.php` — standalone class source code
* `demo.php` (and demo*.php) — demonstaration script
* `calendar.css` — default stylesheet
* `calendar_*.css` — alternative stylesheets