<?php

/**
 * Класс Calendar предназначен для генерации HTML кода календаря. Умеет
 * делать календарь для отдельно взятого месяца или для года, создавать
 * гиперссылки и хинты для заданных дат, имеет многоязыковую поддержку
 * (на данный момент - русский и английский язык) и возможность в гибкой
 * настроки стилевого оформления с помощью внешних CSS.
 * В классе используются только стандартные функции PHP4, никакие внешние 
 * библиотеки для его работы не требуются.
 * 
 * @author Alex Musayev <allyoueverwantedtoknowbutwereafraidtoask@paradigm.ru>
 * @version 0.0.1
 * @link http://paradigm.ru/calendar
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Copyright &copy; 2005, Alex Musayev
 */

/*
	Внутренняя документация выполнена в соответствии 
	со стандартом phpDocumentor 
*/

/**
 * Массив с названиями месяцев на разных языках. Используется классом Calendar.
 * (я знаю про setlocale(), но она не везде работает)
 * Индексы массива - сокращённые названия языков, используемые в качестве 
 * параметров для конструктора Calendar и метода Calendar::setLang(). 
 * @name $MONTHES
 * @global $GLOBALS['MONTHES']
 * @see Calendar, Calendar::setLang()
 */
$MONTHES = array(
		'en' => array('January', 'February', 'March', 'April', 'May', 'June', 
			'July', 'August', 'September', 'October', 'November', 'December'),
		'ru' => array('Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 
			'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь')
	);

/**
 * Массив с сокращёнными названиями дней недели на разных языках. 
 * Индексы массива - сокращённые названия языков, используемые в качестве 
 * параметров для конструктора Calendar и метода Calendar::setLang(). 
 * Последовательность дней недели должна соответствовать значениям date('w') 
 * (0 сообтветствует воскресенью, 1 - понедельнику и т.д.)
 * @name $DOWS
 * @global $GLOBALS['DOWS']
 * @see Calendar, Calendar::setLang()
 */
$DOWS = array(
		'en' => array('Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'),
		'ru' => array('Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб')
	);

/**
 * Опция отображения календаря. Определяет, отмечать ли воскресенья с помощью 
 * специального CSS класса.
 */
define("CLD_MARKSUN", 1);

/**
 * Опция отображения календаря. Определяет, отмечать ли субботы и воскресенья с 
 * помощью CSS класса.
 */
define("CLD_MARKSATSUN", 2);

/**
 * Опция отображения календаря. Определяет, считать ли первым днём недели 
 * понедельник или воскресенье. По-умолчанию первым считается воскресенье.
 */
define("CLD_MONDAYS", 4);

/**
 * Опция отображения календаря. Определяет, использовать ли переносы строки 
 * в генерируемом HTML коде. Необходимо только для отладки. По-умолчанию 
 * переносы выключены.
 */
define("CLD_BREAKS", 8);

class Calendar {
	
	/**
	 * Массив для хранения ссылок, ассоциированных с датами.
	 * @var array
	 * @see addUDateLink(), addDateLink(), removeUDateLink(), 
	 * removeDateLink(), removeDateLinks()
	 * @access private
	 */
	var $dateLinks = array();
	
	/**
	 * Массив для хранения хинтов, ассоциированных с датами.
	 * @var array
	 * @see addUDateTitle(), addDateTitle(), removeUDateTitle(), 
	 * removeDateTitle(), removeDateTitles()
	 * @access private
	 */
	var $dateTitles = array();
	
	
	/**
	 * Язык, на котором будут отображаться названия месяцев и дней недели 
	 * в календаре. Значение переменной должно соответствовать одному 
	 * из индексов в массивах $MONTHES и $DOWS.
	 * @var string
	 * @see setLang()
	 * @access private
	 */
	var $lng = false;
	
	/**
	 * Префикс, используемый для имён всех CSS классов
	 * @var string
	 * @see setCssPrefix()
	 * @access private
	 */
	var $cssPrefix;
	
	/**
	 * Опции отображения календаря. Значение переменной задаётся с помощью
	 *  констант CLD_*, перечислямых через двоичное OR.
	 * @var int
	 * @see setOptions()
	 * @access private
	 */
	var $options = false;
	
	/**
	 * Конструктор
	 * @access public
	 * @param string $_lng Язык, на котором будут выводиться названия дней 
	 * недели и месяцев. После создания объекта он может быть переопределён 
	 * с помощью метода setLang().
	 * @param int $_options Параметры календаря, задаваемые через двоичное OR 
	 * (см. константы CLD_*). После создания объекта, опции можно 
	 * переопределять методом setOptions().
	 * @param int $_cssPrefix Префикс, используемый для имён всех CSS классов. 
	 * После создания объекта он может быть переопределён с помощью метода 
	 * setCssPrefix().
	 * @see setLang(), setOptions(), setCssPrefix()
	 */
	function Calendar($_lng = 'en', $_options = false, $_cssPrefix = 'cld_') {
		global $DOWS;
		if(!isset($DOWS[$_lng])) {
			trigger_error("Undefined language was specified.", E_USER_WARNING);
			return false;
		}
		$this->dows = $DOWS[$_lng];
		$this->setLang($_lng);
		$this->setOptions($_options);
		$this->setCssPrefix($_cssPrefix);
	}
	
	/**
	 * Задаёт язык отображения названий дней недели и месяцев в календаре.
	 * @param string $_lng Код языка. Указанное значение должно соответствовать 
	 * индексам в глобальных массивах $DOWS и $MONTHES. По-умолчанию 
	 * используется английский.
	 * @return bool В зависимости от того, корректный ли задан код языка, метод 
	 * возвращает true или false.
	 */
	function setLang($_lng) {
		global $MONTHES;
		global $DOWS;
		if(isset($MONTHES[$_lng]) && isset($DOWS[$_lng])) {
			$this->lng = $_lng;
			return true;
		} else {
			trigger_error("Calendar language not defined: '".$_lng."'", 
				E_USER_WARNING);
			return false;
		}
	}
	
	/**
	 * Задаёт параметры календаря, перечисляемые через двоичное OR 
	 * (см. константы CLD_*).
	 * @param string $_options Параметры, заданные через двоичное OR.
	 * @return void
	 * @example $clndr->setOptions(CLD_MONDAYS | CLD_MARKSATSUN)
	 */
	function setOptions($_options) {
		$this->options = $_options;
	}
	
	/**
	 * Задаёт префикс, используемый для имён всех CSS классов. С помощью 
	 * этого метода, можно изменять стилевое оформление календаря в рантайме.
	 * @param string $_cssPrefix Строковое значение, с которого начинаются все 
	 * CSS классы.
	 * @return void
	 */
	function setCssPrefix($_cssPrefix) {
		$this->cssPrefix = $_cssPrefix;
	}
	
	/**
	 * Добавляет гиперссылку для заданной даты. Если ссылка была уже задана, 
	 * она будет переопределена.
	 * @access public
	 * @param int $_date Дата в формате Unix timestamp, к которой будет 
	 * привязана ссылка. Можно задавать "неточную" дату - значение часов, минут 
	 * и секунд игнорируется.
	 * @param string $_url Ссылка
	 * @return void
	 * @see addDateLink(), removeUDateLink(), removeDateLink(), 
	 * removeDateLinks()
	 */
	function addUDateLink($_date, $_url) {
		$d = getdate($_date);
		$d = mktime(0, 0, 0, $d["mon"], $d["mday"], $d["year"]);
		$this->dateLinks[$d] = $_url;
	}
	
	/**
	 * Добавляет гиперссылку для заданной даты. Отличается от addUDateLink 
	 * только форматом, в котором определяется дата. Если ссылка была уже 
	 * задана, она будет переопределена.
	 * @access public
	 * @param int $_day Число
	 * @param int $_month Месяц
	 * @param int $_year Год
	 * @param string $_url Ссылка
	 * @see addUDateLink(), removeUDateLink(), removeDateLink(), 
	 * removeDateLinks()
	 */
	function addDateLink($_day, $_month, $_year, $_url) {
		$this->addUDateLink(mktime(0, 0, 0, $_month, $_day, $_year), $_url);
		return true;
	}
	
	/**
	 * Удаляет ссылку для заданной даты (если она определена).
	 * @access public
	 * @param int $_date Дата, для которой необходимо удалить ссылку (имеет 
	 * значение год, месяц и день)
	 * @see addUDateLink(), addDateLink(), removeDateLink(), removeDateLinks()
	 * @return void
	 */
	function removeUDateLink($_date) {
		$d = gethate($_date);
		$d = mktime(0, 0, 0, $d["mon"], $d["mday"], $d["year"]);
		unset($this->dateLinks[$d]);
	}
	
	/**
	 * Удаляет ссылку для заданной даты (если она определена). Отличается 
	 * от removeUDateLink только форматом, в котором определяется дата. 
	 * @access public
	 * @param int $_day Число
	 * @param int $_month Месяц
	 * @param int $_year Год
	 * @see addUDateLink(), addDateLink(), removeUDateLink(), removeDateLinks()
	 * @return void
	 */
	function removeDateLink($_day, $_month, $_year) {
		$this->removeUDateLink(mktime(0, 0, 0, $_month, $_day, $_year));
	}
	
	/**
	 * Удаляет все определённые ссылки.
	 * @access public
	 * @see addUDateLink(), addDateLink(), removeUDateLink(), removeDateLink()
	 * @return void
	 */
	function removeDateLinks() {
		$this->dateLinks = array();
	}
	
	/**
	 * Добавляет хинт для заданной даты. Если хинт был уже задан, он будет 
	 * переопределен.
	 * @access public
	 * @param int $_date Дата в формате Unix timestamp, к которой будет 
	 * привязан хинт. Можно задавать "неточную" дату, значение часов, минут 
	 * и секунд игнорируется.
	 * @param string $_title Текст хинта
	 * @return void
	 */
	function addUDateTitle($_date, $_title) {
		$d = getdate($_date);
		$d = mktime(0, 0, 0, $d["mon"], $d["mday"], $d["year"]);
		$this->dateTitles[$d] = $_title;
	}
	
	/**
	 * Добавляет хинт для заданной даты. Отличается от addUDateTitle только 
	 * форматом, в котором определяется дата.
	 * @access public
	 * @param int $_day Число
	 * @param int $_month Месяц
	 * @param int $_year Год
	 * @param string $_title Текст хинта
	 * @return void
	 */
	function addDateTitle($_day, $_month, $_year, $_title) {
		$this->addUDateTitle(mktime(0, 0, 0, $_month, $_day, $_year), $_title);
	}
	
	/**
	 * Удаляет хинт для заданной даты (если он определен).
	 * @access public
	 * @param int $_date Дата, для которой необходимо удалить ссылку 
	 * (имеет значение год, месяц и день)
	 * @return void
	 */
	function removeUDateTitle($_date) {
		$d = getdate($_date);
		$d = mktime(0, 0, 0, $d["mon"], $d["mday"], $d["year"]);
		unset($this->dateTitles[$d]);
	}
	
	/**
	 * Удаляет хинт для заданной даты (если он определен). Отличается 
	 * от removeUDateTitle только форматом, в котором определяется дата.
	 * @access public
	 * @param int $_day Число
	 * @param int $_month Месяц
	 * @param int $_year Год
	 * @return void
	 */
	function removeDateTitle($_day, $_month, $_year) {
		$this->removeUDateTitle(mktime(0, 0, 0, $_month, $_day, $_year));
	}
	
	/**
	 * Удаляет все определённые хинты.
	 * @access public
	 * @return void
	 */
	function removeDateTitles() {
		$this->dateTitles = array();
	}
	
	/**
	 * Генерирует HTML код для заданного месяца. Дата задаётся в формате Unix 
	 * tiestamp.
	 * @access public
	 * @param int $_date Месяц, для которого необходимо сгенерировать
	 * календарь, задаваемый формате Unix timestamp (значение года, само собой, 
	 * тоже имеет значение; часы, минуты и секунды - игнорируются)
	 * @param bool $_mark Флаг, определяющий необходимость выделить текущую 
	 * дату. По-умолчанию, текущая дата не выделяется.
	 * @return string Сгенерированный HTML код
	 */
	function genUMonth($_date, $_mark = false) {
		$d = getdate($_date);
		$month = $d["mon"];
		$day = $d["mday"];
		$year = $d["year"];
		$wDay = date('w', mktime(0, 0, 0, $month, 1, $year));
		
		$res[] = '<table border="0" cellspacing="0" cellpadding="0" class="'.
			$this->cssPrefix.'month">';
		
		global $MONTHES;
		$monthes = (isset($MONTHES[$this->lng]))?
			$MONTHES[$this->lng]:date('F', $_date);
		
		global $DOWS;
		$dows = $DOWS[$this->lng];
		
		// строка-заголовок с названием месяца
		$res[] = '<tr><th colspan="7" class="'.$this->cssPrefix.'montitle">'.
			$monthes[$month - 1].'</th></tr>';
		
		// опции отображения календаря
		$mondayIsFirst = ($this->options & CLD_MONDAYS);
		$markSat = ($this->options & CLD_MARKSATSUN);
		$markSun = ($this->options & CLD_MARKSUN) || 
			($this->options & CLD_MARKSATSUN);
		
		if($mondayIsFirst) {
			$sunday = array_shift($dows);
			array_push($dows, $sunday);
			$wDay = $wDay?($wDay - 1):6;
		}
		
		// строка-заголовок с названиями дней недели
		$tmp = array();
		for($i = 0; $i < 7; $i++) {
			// опционально выделение суббот и/или воскресенья с помощю 
			// специальных CSS классов
			$weekEnd = ($mondayIsFirst?(($i == 6) && $markSun) || (($i == 5) && 
				$markSat):(($i == 0) && $markSun) || (($i == 6) && 
				$markSat))?'marked':'';
			// определяем имя CSS класса для следующей ячейки
			$cssClass = $this->cssPrefix.(($i % 2)?'light':'dark').$weekEnd;
			$tmp[] = '<th class="'.$cssClass.'">'.$dows[$i].'</th>';
		}
		
		// опредляем, нужен ли разделитель для строк HTML кода
		$s = ($this->options & CLD_BREAKS)?"\n":'';
		$res[] = '<tr>'.implode($s, $tmp).'</tr>';
		
		// значение понадобиться для вычисления количества пробелов вконце 
		// таблицы
		$spacesNum = $wDay;
		
		// пробелы перед первым числом
		$tmp = '<tr>';
		for($i = 0; $i < $wDay; $i++) {
			$tmp .= '<td class="'.$this->cssPrefix.
				(($i % 2)?'light':'dark').'">&nbsp;</td>';
		}
		$res[] = $tmp;
		
		// счётчик недель
		$wcnt = 0;
		
		for($i = 1, $striper = $wDay % 2; $i <= date('t', $_date); 
			$i++, $striper = !$striper) {
			// отмечаем текущий день, если надо
			$today = $_mark && ($i == $day);
			
			// добавляем линки
			$d = mktime(0, 0, 0, $month, $i, $year);
			
			$linkSet = isset($this->dateLinks[$d]);
			$titleSet = isset($this->dateTitles[$d]);
			if($linkSet && $titleSet) {
				$dayCode = '<a href="'.$this->dateLinks[$d].
					'" title="'.$this->dateTitles[$d].'" class="'.
					$this->cssPrefix.'titleddatelink'.'">'.$i.'</a>';
			} elseif($linkSet) {
				$dayCode = '<a href="'.$this->dateLinks[$d].
				'" class="'.$this->cssPrefix.'datelink">'.$i.'</a>';
			} elseif($titleSet) {
				$dayCode = '<em title="'.$this->dateTitles[$d].
					'" class="'.$this->cssPrefix.'titleddate">'.$i.'</a>';
			} else {
				$dayCode = $i;
			}
			
			// далее $wDay используется в качестве определителя дня недели,
			// для выделения суббот и воскресений, и переноса строк
			$wDay = ($wDay + 1) % 7;
			// определяем, нужно ли отметить субботу и/или воскресенье
			$weekEnd = (($wDay == 0) && $markSun) || (($wDay == 6) && 
				$markSat)?'marked':'';
			
			$cssClass = $this->cssPrefix.($today?'today':
				(($striper?'light':'dark').$weekEnd));
			$res[] = '<td class="'.$cssClass.'">'.$dayCode.'</td>';
			if(!$wDay) {
				$res[] = '</tr><tr>';
				$wcnt++;
				$striper = true;
			}
		}
		
		// пробелы в конце
		$spacesNum = (7 - ($spacesNum + $i) % 7) % 7 + 1;
		$tmp = '';
		for($i = 0; $i < $spacesNum; $i++, $striper = !$striper) {
			$tmp .= '<td class="'.$this->cssPrefix.($striper?'light':'dark').
				'">&nbsp;</td>';
		}
		$res[] = $tmp.'</tr>';
		
		// вне зависимости от количества недель в месяце, выравниваем высоту 
		// до 6 строк
		if($wcnt < 5) {
			$res[] = '<tr><td class="'.$this->cssPrefix.
				'dark">&nbsp;</td><td class="'.$this->cssPrefix.
				'light">&nbsp;</td><td class="'.$this->cssPrefix.
				'dark">&nbsp;</td><td class="'.$this->cssPrefix.
				'light">&nbsp;</td><td class="'.$this->cssPrefix.
				'dark">&nbsp;</td><td class="'.$this->cssPrefix.
				'light">&nbsp;</td><td class="'.$this->cssPrefix.
				'dark">&nbsp;</td></tr>';
		}
		
		$res[] = '</table>';
		
		return implode($s, $res);
	}
	
	/**
	 * Генерирует HTML код для заданного месяца из заданного года.
	 * @access public
	 * @param mixed $_day Если параметру задать числовое значение, заданная 
	 * дата будет выделена. Если задать значение false - выделение выполнено 
	 * не будет.
	 * @param int $_month Месяц, для которого необходимо сгенерировать
	 * календарь.
	 * @param int $_year Год, к которому относится заданный в предыдущем 
	 * параметре месяц.
	 * @return string Сгенерированный HTML код
	 */
	function genMonth($_day, $_month, $_year) {
		return $this->genUMonth(mktime(0, 0, 0, $_month, is_numeric($_day)?$_day:1, $_year), is_numeric($_day)?true:false);
	}
	
	/**
	 * Генерирует HTML код для заданного года
	 * @access public
	 * @param int $_date Год, для которого необходимо сгенерировать календарь 
	 * в формате Unix timestamp (значениа месяца часов, минут и секунд 
	 * игнорируются)
	 * @param int $_mark Флаг, определяющий необходимость выделить текущую
	 * дату. По-умолчанию, текущая дата не выделяется.
	 * @param int $_width Количество месяцев, отображемых в один ряд. 
	 * По-умолчанию - 3.
	 * @return string Сгенерированный HTML код
	 * @see genYear()
	 */
	function genUYear($_date, $_mark = false, $_width = 3) {
		$year = date('Y', $_date);
		$mMonth = date('n', $_date);
		
		$res[] = '<table border="0" cellspacing="0" cellpadding="10" class="'.
			$this->cssPrefix.'year">';
		$res[] = '<tr><th colspan="'.$_width.'" class="'.$this->cssPrefix.
			'yeartitle"><big>'.$year.'</big></th></tr>';
		$res[] = '<tr>';
		
		for($i = 1; $i <= 12; $i++) {
			if($mMonth == $i) {
				$monthHtml = $this->genUMonth($_date, $_mark);
			} else {
				$monthHtml = $this->genUMonth(mktime(0, 0, 0, $i, 1, $year));
			}
			$res[] = '<td valign="top" class="'.$this->cssPrefix.
				(($i % $_width)?'monthcell':'monthlastcell').'">'.$monthHtml.
				'</td>';
			if(!($i % $_width)) {
				$res[] = '</tr><tr>';
			}
		}
		
		$res[] = $tmp.'</tr>';
		$res[] = '</table>';
		
		// опредляем, нужен ли разделитель для строк HTML кода
		$s = ($this->options & CLD_BREAKS)?"\n":'';
		return implode($s, $res);
	}
	
	/**
	 * Генерирует HTML код для заданного года. Отличается от genUYear() только 
	 * форматом задания даты.
	 * @access public
	 * @param int $_day Число (имеет значение только в том случае, если его 
	 * необходимо выделить; в противном случае, можно задать false)
	 * @param int $_month Месяц (имеет значение только в том случае, если 
	 * необходимо выделить заданное число; в противном случае, можно задать 
	 * false)
	 * @param int $_year Год, для которого необходимо сгенерировать календарь
	 * @param int $_mark Флаг, определяющий необходимость выделить текущую
	 * дату. По-умолчанию, текущая дата не выделяется.
	 * @param int $_width Количество месяцев, отображемых в один ряд. 
	 * По-умолчанию - 3.
	 * @return string Сгенерированный HTML код
	 * @see genUYear()
	 */
	function genYear($_day, $_month, $_year, $_mark = false, $_width = 3) {
		return $this->genUYear(mktime(0, 0, 0, is_numeric($_month)?$_month:1, 
		is_numeric($_day)?$_day:1, $_year), $_mark, $_width);
	}
}

?>