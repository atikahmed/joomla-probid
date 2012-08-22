<?php
/**
* @version 1.0.0
* @package RSMembership! 1.0.0
* @copyright (C) 2009-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class RSMembershipModelReports extends JModel
{
	var $_db;
	var $_memberships;
	var $_membership_names;
	
	var $min = 0;
	var $avg = 0;
	var $max = 0;
	var $count = 0;
	var $total = 0;
	
	var $viewin = '';
	
	function __construct()
	{
		parent::__construct();

		// large databases need lots of memory
		ini_set('memory_limit', '128M');

		$this->_db = JFactory::getDBO();

		$this->_db->setQuery("SET SQL_BIG_SELECTS=1");
		$this->_db->query();

		$document =& JFactory::getDocument();

		$document->addScript(JURI::base().'components/com_rsmembership/assets/js/rsmembership.js');
		$document->addScript(JURI::base().'components/com_rsmembership/assets/js/bluff/js-class.js');
		$document->addScript(JURI::base().'components/com_rsmembership/assets/js/bluff/bluff-min.js');
		$document->addScript(JURI::base().'components/com_rsmembership/assets/js/bluff/excanvas.js');
		$document->addScript(JURI::base().'components/com_rsmembership/assets/js/reports.js');

		jimport('joomla.plugin.helper');
		if (RSMembershipHelper::isJ16() || JPluginHelper::isEnabled('system','mtupgrade'))
			$document->addScript(JURI::base().'components/com_rsmembership/assets/js/rainbow12.js');
		else
			$document->addScript(JURI::base().'components/com_rsmembership/assets/js/rainbow.js');
		
		$document->addStyleSheet(JURI::base().'components/com_rsmembership/assets/css/reports.css');
		
		$this->_getMemberships();
	}

	function _getMemberships()
	{
		$this->_memberships = $this->_getList("SELECT * FROM #__rsmembership_memberships ORDER BY `ordering` ASC");
		$this->_membership_names = array();
		foreach ($this->_memberships as $membership)
			$this->_membership_names[$membership->id] = $membership->name;
	}
		 
	function getcountMemberships()
	{
		return count($this->_memberships);

	}
	
	function _getSubscribers()
	{
		return false;
	}

	function getCustomer()
	{		
		return '<em>'.JText::_('RSM_NO_USER_SELECTED').'</em>';
	}

	function getReport()
	{
		$report = JRequest::getCmd('report');

		switch ($report)
		{
			default:
			case 'report_1':
				return 'report_1';
			break;
			
			case 'report_2':
				return $report;
			break;
		}
	}

	function getMemberships()
	{
	
				$return = '';
		
		$src = JURI::base().'components/com_rsmembership/assets/images/rainbow/color.gif';
		$title = JText::_('RSM_CHANGE_COLOR_DESC');
		$alt = JText::_('RSM_CHANGE_COLOR');
		
		$memberships = $this->_memberships;
		foreach ($memberships as $i => $membership)
		{
			$checked = 'checked="checked"';
			$return .=
			'<p>
			<div style="float: left">'.
				' <input type="checkbox" name="memberships[]" id="membership'.$i.'" value="'.$membership->id.'" '.$checked.' />'.
				' <label for="membership'.$i.'">'.$membership->name.'</label>'.
				' </div>'.
				' <div style="float: right">'.
				' <img id="change_color_membership_'.$i.'i" class="rsm_color hasTip" title="'.$title.'" src="'.$src.'" alt="'.$alt.'" />'.
				' <input class="rsm_change_color" id="color_membership_'.$i.'" size="10" type="text" name="" value="'.$this->getColor($i).'" style="background: '.$this->getColor($i).';" />'.
				' </div>'.
				' <span class="rsmembership_clear"></span>'.
			'</p>';
		}

		return $return;

	}

	function getMembershipsTransactions()
	{
		$return = '';

		$memberships = $this->_memberships;
		foreach ($memberships as $i => $membership)
		{
			$checked = 'checked="checked"';
			$return .=
			'<p>
			<div style="float: left">'.
				' <input type="checkbox" name="memberships_transactions[]" id="membership_transactions'.$i.'" value="'.$membership->id.'" '.$checked.' />'.
				' <label for="membership_transactions'.$i.'">'.$membership->name.'</label>'.
				' </div>'.
				' <span class="rsmembership_clear"></span>'.
			'</p>';
		}

		return $return;
	}

	function getGateways()
	{
		$query = "SELECT DISTINCT gateway FROM #__rsmembership_transactions Order By `gateway` ASC";
		$this->_db->setQuery($query);
		$gateways = $this->_db->loadObjectList();

		$gateway_options = '';

		foreach($gateways as $i => $gateway)
		{
			$checked = 'checked="checked"';
			$gateway_options .=
			'<p>'.
				' <div style="float: left">'.
				' <input type="checkbox" name="gateways[]" id="gateway'.$i.'" value="'.$gateway->gateway.'" '.$checked.' />'.
				' <label for="gateway'.$i.'">'.$gateway->gateway.'</label>'.
				' </div>'.' <span class="rsmembership_clear"></span>'.
			'</p>';
		}

		return $gateway_options;
	}

	function getTransactionTypes()
	{
	$types = array('new','upgrade','addextra','renew');

		$src = JURI::base().'components/com_rsmembership/assets/images/rainbow/color.gif';
		$title = JText::_('RSM_CHANGE_COLOR_DESC');
		$alt = JText::_('RSM_CHANGE_COLOR');


		$transaction_types = '';

		foreach($types as $i => $type)
		{
			$checked = 'checked="checked"';
			$transaction_types .='
			<p>
				<div style="float: left">
				<input type="checkbox" name="transaction_types[]" id="transaction_type'.$i.'" value="'.$type.'" '.$checked.' />
				<label for="transaction_type'.$i.'">'.JText::_('RSM_TRANSACTION_'.strtoupper($type)).'</label>
				</div>
				<div style="float: right">
				<img id="change_transaction_types_'.($i).'i" class="rsm_color hasTip" title="'.$title.'" src="'.$src.'" alt="'.$alt.'" />
				<input class="rsm_change_color" id="color_transaction_types_'.($i).'" size="10" type="text" name="" value="'.$this->getColor($i).'" style="background: '.$this->getColor($i).'" />
				</div>
				<span class="rsmembership_clear"></span>
			</p>';

		}

		return $transaction_types;
	}

	function getReportData()
	{
		$return = array();
		
		$filter = array();
		$where = "";
		// from, to
		$from_date = JRequest::getVar('from_date');
		$to_date = JRequest::getVar('to_date');
		
		$date_column = ($this->getReport() == 'report_2' ? 'date' : 'membership_start');

		if ($from_date || $to_date)
		{
			$start = @strtotime($from_date);
			$stop  = @strtotime($to_date);
			
			if ($start && $stop)
			{
				$filter['date'] = " AND (`".$date_column."` > $start AND `".$date_column."` < $stop)";
				$where .= $filter['date'];
			}
			elseif ($start)
			{
				$filter['date'] = " AND `".$date_column."` > $start";
				$where .= $filter['date'];
			}
			elseif ($stop)
			{
				$filter['date'] = " AND `".$date_column."` < $stop";
				$where .= $filter['date'];
			}
		}
		
		$unit = JRequest::getVar('unit');
		$format = 'Y-m-d';
		if ($unit == 'day')
			$format = 'Y-m-d';
		elseif ($unit == 'month')
			$format = 'Y-m';
		elseif ($unit == 'year')
			$format = 'Y';
		elseif ($unit == 'quarter')
			$format = 'Y-m';

		
		$user_id = JRequest::getInt('user_id');
		if (!empty($user_id))
		{
			$filter['user_id'] = " AND user_id='".$user_id."'";
			$where .= $filter['user_id'];
		}
		if($this->getReport() == 'report_2')
		{
			$transaction_types = JRequest::getVar('transaction_types');
					
			if (!empty($transaction_types))
			{
				$filter['transaction_types'] = " AND `type` IN ('".implode("', '", $transaction_types)."')";
				$where .= $filter['transaction_types'];
			}

			$gateways = JRequest::getVar('gateways');
			if (!empty($gateways))
			{
				$filter['gateways'] = " AND gateway IN ('".implode("', '", $gateways)."')";
				$where .= $filter['gateways'];
			}

			$memberships = JRequest::getVar('memberships_transactions');
			
			JArrayHelper::toInteger($memberships, array());
			
			if (!empty($memberships) && !empty($transaction_types))
			{
				$filter['membership_id'] = " AND (";

				foreach($memberships as $mem){

					$filter['membership_id'] .= "params LIKE 'membership_id=".$mem."' ";
					$filter['membership_id'] .= " OR params LIKE '%;membership_id=".$mem."' ";
					$filter['membership_id'] .= " OR params LIKE 'membership_id=".$mem.";%' ";
					$filter['membership_id'] .= " OR params LIKE '%;membership_id=".$mem.";%' ";
					$filter['membership_id'] .= " OR params LIKE '%;membership_id=".$mem.";%' ";
					$filter['membership_id'] .= " OR params LIKE '%;from_id=".$mem.";%' ";
					$filter['membership_id'] .= " OR params LIKE '%;to_id=".$mem."' ";
					if($mem != end($memberships)) $filter['membership_id'] .= "OR ";
				}
					$filter['membership_id'] .= ")";
					$where .= $filter['membership_id'];
			}

			$status = JRequest::getVar('status_transactions');
			if (!empty($status))
			{
				$filter['status_transactions'] = " AND status IN ('".strtolower(implode("' ,'", $status))."')";
				$where .= $filter['status_transactions'];
			}
		}
		else 
		{
			$memberships = JRequest::getVar('memberships');
			JArrayHelper::toInteger($memberships, array());
			if (!empty($memberships))
			{
				$membership_column = ($this->getReport() == 'report_2' ? 'params' : 'membership_id');
				$filter['membership_id'] = " AND membership_id IN (".implode(',', $memberships).")";

				$where .= $filter['membership_id'];
			}
			
			$status = JRequest::getVar('status_memberships');

			if (!empty($status))
			{
				$filter['status_memberships'] = " AND status IN (".strtolower(implode(',', $status)).")";
				$where .= $filter['status_memberships'];
			}
		}

		$price_from = JRequest::getVar('price_from');
		if (!empty($price_from))
		{
			$filter['price_from'] = " AND price >= (".$price_from.")";
			$where .= $filter['price_from'];
		}

		$price_to = JRequest::getVar('price_to');
		if (!empty($price_to))
		{
			$filter['price_to'] = " AND price <= (".$price_to.")";
			$where .= $filter['price_to'];
		}

		// ordering
		$orderby = " ORDER BY `".($this->getReport() == 'report_2' ? 'date' : 'membership_start') ."` ASC";

		switch ($this->getReport())
		{	
			// number of subscribers
			case 'report_1':
				// query
				$query = "SELECT membership_id, ".$date_column." FROM #__rsmembership_membership_users WHERE 1";
				$query = $query.$where.$orderby;
				$this->_db->setQuery($query);
				$subscribers = $this->_db->loadObjectList();

				foreach ($memberships as $membership)
				{
					$membership = $this->getMembershipName($membership);
					$return['memberships'][$membership] = array();
				}
				
				if(!empty($subscribers))
				foreach ($subscribers as $subscriber)
				{
					if ($unit == 'quarter')
						$format = $this->getQuarter(RSMembershipHelper::getCurrentDate($subscriber->membership_start));

					$date = date($format, RSMembershipHelper::getCurrentDate($subscriber->membership_start));

					$membership = $this->getMembershipName($subscriber->membership_id);
					@$return['units'][$date] = $date;
					@$return['memberships'][$membership][$date] += 1;
					@$return['totals'][$date] += 1;

				}

				if (!empty($return['totals']))
				{
					$this->min = $this->max = max($return['totals']);
					
					foreach ($return['units'] as $date)
					{
						foreach ($memberships as $membership)
						{
							$membership = $this->getMembershipName($membership);
							if (empty($return['memberships'][$membership][$date]))
							{
								$return['memberships'][$membership][$date] = 0;
								if (empty($return['totals'][$date]))
									$return['totals'][$date] = 0;
							}
						}
						
						// min
						if (!empty($return['totals'][$date]))
							$this->min = min($this->min, $return['totals'][$date]);
					}
					
					foreach ($return['memberships'] as $return_membership => $return_values)
						ksort($return['memberships'][$return_membership]);
					
					// total	
					$this->total = array_sum($return['totals']);
				
					// avg
					$this->avg = floor(array_sum($return['totals'])/count($return['totals']));
				}
			break;
			
			case 'report_2':
				// query
				$query = "SELECT id,type,params, ".$date_column." FROM #__rsmembership_transactions WHERE 1";
				$query = $query.$where.$orderby;

				$this->_db->setQuery($query);
				$transactions = $this->_db->loadObjectList();
				
				if(!empty($transaction_types))
				foreach ($transaction_types as $type)
					$return['transactions'][JText::_('RSM_TRANSACTION_'.strtoupper($type))] = array();
					
				if(!empty($transactions))
				foreach ($transactions as $i => $transaction)
				{
					if ($unit == 'quarter')
						$format = $this->getQuarter(RSMembershipHelper::getCurrentDate($transaction->$date_column));

					$date = date($format, RSMembershipHelper::getCurrentDate($transaction->$date_column));
					@$return['units'][$date] = $date;
					@$return['transactions'][JText::_('RSM_TRANSACTION_'.strtoupper($transaction->type))][$date] += 1;
					@$return['totals'][$date] += 1;
				}

				if (!empty($return['totals']))
				{
					$this->min = $this->max = max($return['totals']);
					
					foreach ($return['units'] as $date)
					{
						foreach ($transactions as $transaction)
						{
							if (empty($return['transactions'][JText::_('RSM_TRANSACTION_'.strtoupper($transaction->type))][$date]))
							{
								$return['transactions'][JText::_('RSM_TRANSACTION_'.strtoupper($transaction->type))][$date] = 0;
								if (!empty($return['totals'][$date]))
									$return['totals'][$date] = 0;
							}
						}

						// min
						if (!empty($return['totals'][$date]))
							$this->min = min($this->min, $return['totals'][$date]);
					}

					foreach ($return['transactions'] as $return_transaction => $return_values)
						ksort($return['transactions'][$return_transaction]);

					// total
					$this->total = array_sum($return['totals']);

					
					// avg
//					$this->avg = floor($this->total/count($return['totals']));
					
					//my avg
					$this->avg = ($this->min+$this->max)/2;
//die(var_dump($this->myavg));					
				}
			break;

		}

		unset($return['totals']);
		return $return;
	}

	function getMembershipName($id)
	{
		return @$this->_membership_names[$id];
	}
	
	function getStaffName($id)
	{
		return @$this->_staff_names[$id];
	}
	
	function getQuarter($date)
	{
		$q = (int)floor(date('m', $date) / 3.1) + 1;
		return "Y Q$q";
	}
	
	function getMin()
	{
		$unit = JRequest::getVar('unit');
		return $this->getNumberFormat($this->min).' '.$this->getViewIn().' '.' / '.JText::_('RSM_'.$unit);
	}
	
	function getAvg()
	{
		$unit = JRequest::getVar('unit');
		return $this->getNumberFormat($this->avg).' '.$this->getViewIn().' '.' / '.JText::_('RSM_'.$unit);
	}
	
	function getMax()
	{
		$unit = JRequest::getVar('unit');
		return $this->getNumberFormat($this->max).' '.$this->getViewIn().' '.' / '.JText::_('RSM_'.$unit);
	}
	
	function getTotal()
	{
		return $this->getNumberFormat($this->total);
	}
	
	function getNumberFormat($number)
	{
		return number_format($number, 2, '.', '');
	}
	
	function getViewIn()
	{
		$report = $this->getReport();
		
		switch ($report)
		{
			case 'report_1':
			case 'report_6':
				return JText::_('RSM_MEMBERSHIPS');
			break;
			
			case 'report_2':
			case 'report_5':
				switch ($this->viewin)
				{
					default:
					case 60:
						return JText::_('RSM_MINUTES');
					break;
					
					case 3600:
						return JText::_('RSM_HOURS');
					break;
					
					case 86400:
						return JText::_('RSM_DAYS');
					break;
				}
			break;
			
			case 'report_3':
				return JText::_('RST_TICKET_REPLIES');
			break;
			
			case 'report_4':
				return JText::_('RST_STARS');
			break;
		}
	}

	function getIE()
	{
		if (preg_match("#MSIE#i", $_SERVER['HTTP_USER_AGENT']) && !preg_match("#MSIE 9#", $_SERVER['HTTP_USER_AGENT']))
			return true;
		
		return false;
	}
	
	function getColor($i)
	{
		$colors = array('#3366FF', '#6633FF', '#CC33FF', '#FF33CC', '#33CCFF', '#003DF5', '#002EB8', '#FF3366', '#33FFCC', '#B88A00', '#F5B800', '#FF6633', '#33FF66', '#66FF33', '#CCFF33', '#FFCC33', '#002080', '#200080', '#600080', '#800060', '#006080', '#002FBD', '#003EFA', '#800020', '#008060', '#FABB00', '#BD8E00', '#802000', '#008020', '#208000', '#608000', '#806000');
		
		if ($i > count($colors) - 1)
		{
			$t = floor($i / (count($colors) - 1));
			$i = floor($i - $t);
		}
		
		return $colors[$i];
	}
}
?>