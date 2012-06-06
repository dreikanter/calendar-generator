<?php

/**
 * ����� Calendar ������������ ��� ��������� HTML ���� ���������. �����
 * ������ ��������� ��� �������� ������� ������ ��� ��� ����, ���������
 * ����������� � ����� ��� �������� ���, ����� ������������� ���������
 * (�� ������ ������ - ������� � ���������� ����) � ����������� � ������
 * �������� ��������� ���������� � ������� ������� CSS.
 * � ������ ������������ ������ ����������� ������� PHP4, ������� ������� 
 * ���������� ��� ��� ������ �� ���������.
 * 
 * @author Alex Musayev <allyoueverwantedtoknowbutwereafraidtoask@paradigm.ru>
 * @version 0.0.1
 * @link http://paradigm.ru/calendar
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Copyright &copy; 2005, Alex Musayev
 */

/*
	���������� ������������ ��������� � ������������ 
	�� ���������� phpDocumentor 
*/

/**
 * ������ � ���������� ������� �� ������ ������. ������������ ������� Calendar.
 * (� ���� ��� setlocale(), �� ��� �� ����� ��������)
 * ������� ������� - ����������� �������� ������, ������������ � �������� 
 * ���������� ��� ������������ Calendar � ������ Calendar::setLang(). 
 * @name $MONTHES
 * @global $GLOBALS['MONTHES']
 * @see Calendar, Calendar::setLang()
 */
$MONTHES = array(
		'en' => array('January', 'February', 'March', 'April', 'May', 'June', 
			'July', 'August', 'September', 'October', 'November', 'December'),
		'ru' => array('������', '�������', '����', '������', '���', '����', 
			'����', '������', '��������', '�������', '������', '�������')
	);

/**
 * ������ � ������������ ���������� ���� ������ �� ������ ������. 
 * ������� ������� - ����������� �������� ������, ������������ � �������� 
 * ���������� ��� ������������ Calendar � ������ Calendar::setLang(). 
 * ������������������ ���� ������ ������ ��������������� ��������� date('w') 
 * (0 �������������� �����������, 1 - ������������ � �.�.)
 * @name $DOWS
 * @global $GLOBALS['DOWS']
 * @see Calendar, Calendar::setLang()
 */
$DOWS = array(
		'en' => array('Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'),
		'ru' => array('��', '��', '��', '��', '��', '��', '��')
	);

/**
 * ����� ����������� ���������. ����������, �������� �� ����������� � ������� 
 * ������������ CSS ������.
 */
define("CLD_MARKSUN", 1);

/**
 * ����� ����������� ���������. ����������, �������� �� ������� � ����������� � 
 * ������� CSS ������.
 */
define("CLD_MARKSATSUN", 2);

/**
 * ����� ����������� ���������. ����������, ������� �� ������ ��� ������ 
 * ����������� ��� �����������. ��-��������� ������ ��������� �����������.
 */
define("CLD_MONDAYS", 4);

/**
 * ����� ����������� ���������. ����������, ������������ �� �������� ������ 
 * � ������������ HTML ����. ���������� ������ ��� �������. ��-��������� 
 * �������� ���������.
 */
define("CLD_BREAKS", 8);

class Calendar {
	
	/**
	 * ������ ��� �������� ������, ��������������� � ������.
	 * @var array
	 * @see addUDateLink(), addDateLink(), removeUDateLink(), 
	 * removeDateLink(), removeDateLinks()
	 * @access private
	 */
	var $dateLinks = array();
	
	/**
	 * ������ ��� �������� ������, ��������������� � ������.
	 * @var array
	 * @see addUDateTitle(), addDateTitle(), removeUDateTitle(), 
	 * removeDateTitle(), removeDateTitles()
	 * @access private
	 */
	var $dateTitles = array();
	
	
	/**
	 * ����, �� ������� ����� ������������ �������� ������� � ���� ������ 
	 * � ���������. �������� ���������� ������ ��������������� ������ 
	 * �� �������� � �������� $MONTHES � $DOWS.
	 * @var string
	 * @see setLang()
	 * @access private
	 */
	var $lng = false;
	
	/**
	 * �������, ������������ ��� ��� ���� CSS �������
	 * @var string
	 * @see setCssPrefix()
	 * @access private
	 */
	var $cssPrefix;
	
	/**
	 * ����� ����������� ���������. �������� ���������� ������� � �������
	 *  �������� CLD_*, ������������ ����� �������� OR.
	 * @var int
	 * @see setOptions()
	 * @access private
	 */
	var $options = false;
	
	/**
	 * �����������
	 * @access public
	 * @param string $_lng ����, �� ������� ����� ���������� �������� ���� 
	 * ������ � �������. ����� �������� ������� �� ����� ���� ������������ 
	 * � ������� ������ setLang().
	 * @param int $_options ��������� ���������, ���������� ����� �������� OR 
	 * (��. ��������� CLD_*). ����� �������� �������, ����� ����� 
	 * �������������� ������� setOptions().
	 * @param int $_cssPrefix �������, ������������ ��� ��� ���� CSS �������. 
	 * ����� �������� ������� �� ����� ���� ������������ � ������� ������ 
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
	 * ����� ���� ����������� �������� ���� ������ � ������� � ���������.
	 * @param string $_lng ��� �����. ��������� �������� ������ ��������������� 
	 * �������� � ���������� �������� $DOWS � $MONTHES. ��-��������� 
	 * ������������ ����������.
	 * @return bool � ����������� �� ����, ���������� �� ����� ��� �����, ����� 
	 * ���������� true ��� false.
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
	 * ����� ��������� ���������, ������������� ����� �������� OR 
	 * (��. ��������� CLD_*).
	 * @param string $_options ���������, �������� ����� �������� OR.
	 * @return void
	 * @example $clndr->setOptions(CLD_MONDAYS | CLD_MARKSATSUN)
	 */
	function setOptions($_options) {
		$this->options = $_options;
	}
	
	/**
	 * ����� �������, ������������ ��� ��� ���� CSS �������. � ������� 
	 * ����� ������, ����� �������� �������� ���������� ��������� � ��������.
	 * @param string $_cssPrefix ��������� ��������, � �������� ���������� ��� 
	 * CSS ������.
	 * @return void
	 */
	function setCssPrefix($_cssPrefix) {
		$this->cssPrefix = $_cssPrefix;
	}
	
	/**
	 * ��������� ����������� ��� �������� ����. ���� ������ ���� ��� ������, 
	 * ��� ����� ��������������.
	 * @access public
	 * @param int $_date ���� � ������� Unix timestamp, � ������� ����� 
	 * ��������� ������. ����� �������� "��������" ���� - �������� �����, ����� 
	 * � ������ ������������.
	 * @param string $_url ������
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
	 * ��������� ����������� ��� �������� ����. ���������� �� addUDateLink 
	 * ������ ��������, � ������� ������������ ����. ���� ������ ���� ��� 
	 * ������, ��� ����� ��������������.
	 * @access public
	 * @param int $_day �����
	 * @param int $_month �����
	 * @param int $_year ���
	 * @param string $_url ������
	 * @see addUDateLink(), removeUDateLink(), removeDateLink(), 
	 * removeDateLinks()
	 */
	function addDateLink($_day, $_month, $_year, $_url) {
		$this->addUDateLink(mktime(0, 0, 0, $_month, $_day, $_year), $_url);
		return true;
	}
	
	/**
	 * ������� ������ ��� �������� ���� (���� ��� ����������).
	 * @access public
	 * @param int $_date ����, ��� ������� ���������� ������� ������ (����� 
	 * �������� ���, ����� � ����)
	 * @see addUDateLink(), addDateLink(), removeDateLink(), removeDateLinks()
	 * @return void
	 */
	function removeUDateLink($_date) {
		$d = gethate($_date);
		$d = mktime(0, 0, 0, $d["mon"], $d["mday"], $d["year"]);
		unset($this->dateLinks[$d]);
	}
	
	/**
	 * ������� ������ ��� �������� ���� (���� ��� ����������). ���������� 
	 * �� removeUDateLink ������ ��������, � ������� ������������ ����. 
	 * @access public
	 * @param int $_day �����
	 * @param int $_month �����
	 * @param int $_year ���
	 * @see addUDateLink(), addDateLink(), removeUDateLink(), removeDateLinks()
	 * @return void
	 */
	function removeDateLink($_day, $_month, $_year) {
		$this->removeUDateLink(mktime(0, 0, 0, $_month, $_day, $_year));
	}
	
	/**
	 * ������� ��� ����������� ������.
	 * @access public
	 * @see addUDateLink(), addDateLink(), removeUDateLink(), removeDateLink()
	 * @return void
	 */
	function removeDateLinks() {
		$this->dateLinks = array();
	}
	
	/**
	 * ��������� ���� ��� �������� ����. ���� ���� ��� ��� �����, �� ����� 
	 * �������������.
	 * @access public
	 * @param int $_date ���� � ������� Unix timestamp, � ������� ����� 
	 * �������� ����. ����� �������� "��������" ����, �������� �����, ����� 
	 * � ������ ������������.
	 * @param string $_title ����� �����
	 * @return void
	 */
	function addUDateTitle($_date, $_title) {
		$d = getdate($_date);
		$d = mktime(0, 0, 0, $d["mon"], $d["mday"], $d["year"]);
		$this->dateTitles[$d] = $_title;
	}
	
	/**
	 * ��������� ���� ��� �������� ����. ���������� �� addUDateTitle ������ 
	 * ��������, � ������� ������������ ����.
	 * @access public
	 * @param int $_day �����
	 * @param int $_month �����
	 * @param int $_year ���
	 * @param string $_title ����� �����
	 * @return void
	 */
	function addDateTitle($_day, $_month, $_year, $_title) {
		$this->addUDateTitle(mktime(0, 0, 0, $_month, $_day, $_year), $_title);
	}
	
	/**
	 * ������� ���� ��� �������� ���� (���� �� ���������).
	 * @access public
	 * @param int $_date ����, ��� ������� ���������� ������� ������ 
	 * (����� �������� ���, ����� � ����)
	 * @return void
	 */
	function removeUDateTitle($_date) {
		$d = getdate($_date);
		$d = mktime(0, 0, 0, $d["mon"], $d["mday"], $d["year"]);
		unset($this->dateTitles[$d]);
	}
	
	/**
	 * ������� ���� ��� �������� ���� (���� �� ���������). ���������� 
	 * �� removeUDateTitle ������ ��������, � ������� ������������ ����.
	 * @access public
	 * @param int $_day �����
	 * @param int $_month �����
	 * @param int $_year ���
	 * @return void
	 */
	function removeDateTitle($_day, $_month, $_year) {
		$this->removeUDateTitle(mktime(0, 0, 0, $_month, $_day, $_year));
	}
	
	/**
	 * ������� ��� ����������� �����.
	 * @access public
	 * @return void
	 */
	function removeDateTitles() {
		$this->dateTitles = array();
	}
	
	/**
	 * ���������� HTML ��� ��� ��������� ������. ���� ������� � ������� Unix 
	 * tiestamp.
	 * @access public
	 * @param int $_date �����, ��� �������� ���������� �������������
	 * ���������, ���������� ������� Unix timestamp (�������� ����, ���� �����, 
	 * ���� ����� ��������; ����, ������ � ������� - ������������)
	 * @param bool $_mark ����, ������������ ������������� �������� ������� 
	 * ����. ��-���������, ������� ���� �� ����������.
	 * @return string ��������������� HTML ���
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
		
		// ������-��������� � ��������� ������
		$res[] = '<tr><th colspan="7" class="'.$this->cssPrefix.'montitle">'.
			$monthes[$month - 1].'</th></tr>';
		
		// ����� ����������� ���������
		$mondayIsFirst = ($this->options & CLD_MONDAYS);
		$markSat = ($this->options & CLD_MARKSATSUN);
		$markSun = ($this->options & CLD_MARKSUN) || 
			($this->options & CLD_MARKSATSUN);
		
		if($mondayIsFirst) {
			$sunday = array_shift($dows);
			array_push($dows, $sunday);
			$wDay = $wDay?($wDay - 1):6;
		}
		
		// ������-��������� � ���������� ���� ������
		$tmp = array();
		for($i = 0; $i < 7; $i++) {
			// ����������� ��������� ������ �/��� ����������� � ������ 
			// ����������� CSS �������
			$weekEnd = ($mondayIsFirst?(($i == 6) && $markSun) || (($i == 5) && 
				$markSat):(($i == 0) && $markSun) || (($i == 6) && 
				$markSat))?'marked':'';
			// ���������� ��� CSS ������ ��� ��������� ������
			$cssClass = $this->cssPrefix.(($i % 2)?'light':'dark').$weekEnd;
			$tmp[] = '<th class="'.$cssClass.'">'.$dows[$i].'</th>';
		}
		
		// ���������, ����� �� ����������� ��� ����� HTML ����
		$s = ($this->options & CLD_BREAKS)?"\n":'';
		$res[] = '<tr>'.implode($s, $tmp).'</tr>';
		
		// �������� ������������ ��� ���������� ���������� �������� ������ 
		// �������
		$spacesNum = $wDay;
		
		// ������� ����� ������ ������
		$tmp = '<tr>';
		for($i = 0; $i < $wDay; $i++) {
			$tmp .= '<td class="'.$this->cssPrefix.
				(($i % 2)?'light':'dark').'">&nbsp;</td>';
		}
		$res[] = $tmp;
		
		// ������� ������
		$wcnt = 0;
		
		for($i = 1, $striper = $wDay % 2; $i <= date('t', $_date); 
			$i++, $striper = !$striper) {
			// �������� ������� ����, ���� ����
			$today = $_mark && ($i == $day);
			
			// ��������� �����
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
			
			// ����� $wDay ������������ � �������� ������������ ��� ������,
			// ��� ��������� ������ � �����������, � �������� �����
			$wDay = ($wDay + 1) % 7;
			// ����������, ����� �� �������� ������� �/��� �����������
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
		
		// ������� � �����
		$spacesNum = (7 - ($spacesNum + $i) % 7) % 7 + 1;
		$tmp = '';
		for($i = 0; $i < $spacesNum; $i++, $striper = !$striper) {
			$tmp .= '<td class="'.$this->cssPrefix.($striper?'light':'dark').
				'">&nbsp;</td>';
		}
		$res[] = $tmp.'</tr>';
		
		// ��� ����������� �� ���������� ������ � ������, ����������� ������ 
		// �� 6 �����
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
	 * ���������� HTML ��� ��� ��������� ������ �� ��������� ����.
	 * @access public
	 * @param mixed $_day ���� ��������� ������ �������� ��������, �������� 
	 * ���� ����� ��������. ���� ������ �������� false - ��������� ��������� 
	 * �� �����.
	 * @param int $_month �����, ��� �������� ���������� �������������
	 * ���������.
	 * @param int $_year ���, � �������� ��������� �������� � ���������� 
	 * ��������� �����.
	 * @return string ��������������� HTML ���
	 */
	function genMonth($_day, $_month, $_year) {
		return $this->genUMonth(mktime(0, 0, 0, $_month, is_numeric($_day)?$_day:1, $_year), is_numeric($_day)?true:false);
	}
	
	/**
	 * ���������� HTML ��� ��� ��������� ����
	 * @access public
	 * @param int $_date ���, ��� �������� ���������� ������������� ��������� 
	 * � ������� Unix timestamp (�������� ������ �����, ����� � ������ 
	 * ������������)
	 * @param int $_mark ����, ������������ ������������� �������� �������
	 * ����. ��-���������, ������� ���� �� ����������.
	 * @param int $_width ���������� �������, ����������� � ���� ���. 
	 * ��-��������� - 3.
	 * @return string ��������������� HTML ���
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
		
		// ���������, ����� �� ����������� ��� ����� HTML ����
		$s = ($this->options & CLD_BREAKS)?"\n":'';
		return implode($s, $res);
	}
	
	/**
	 * ���������� HTML ��� ��� ��������� ����. ���������� �� genUYear() ������ 
	 * �������� ������� ����.
	 * @access public
	 * @param int $_day ����� (����� �������� ������ � ��� ������, ���� ��� 
	 * ���������� ��������; � ��������� ������, ����� ������ false)
	 * @param int $_month ����� (����� �������� ������ � ��� ������, ���� 
	 * ���������� �������� �������� �����; � ��������� ������, ����� ������ 
	 * false)
	 * @param int $_year ���, ��� �������� ���������� ������������� ���������
	 * @param int $_mark ����, ������������ ������������� �������� �������
	 * ����. ��-���������, ������� ���� �� ����������.
	 * @param int $_width ���������� �������, ����������� � ���� ���. 
	 * ��-��������� - 3.
	 * @return string ��������������� HTML ���
	 * @see genUYear()
	 */
	function genYear($_day, $_month, $_year, $_mark = false, $_width = 3) {
		return $this->genUYear(mktime(0, 0, 0, is_numeric($_month)?$_month:1, 
		is_numeric($_day)?$_day:1, $_year), $_mark, $_width);
	}
}

?>