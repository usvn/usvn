<?php

class Default_Model_Ticket extends Default_Model_Abstract
{
		/* Relations */
		protected $_milestone;
		protected $_creator;
		protected $_modificator;
		protected $_assignedTo;

    public function __construct($row = null)
    {
			if ($row instanceof Zend_Db_Table_Row)
				$this->_initWithRow($row);
			elseif ($row !== null)
				$this->_initNew($row);
		}

    protected function _initNew(array $values)
    {
      if (empty($values['creator_id']))
        throw new Exception("Need the creator_id");
      $values['modificator_id'] = $values['creator_id'];
      $values['creation_date'] = new Zend_Date(null);
      $values['modification_date'] = new Zend_Date(null);
      parent::_initNew($values);
    }

    public function updateWithValues($values)
    {
      unset($values['creator_id']);
      unset($values['creation_date']);
      if (empty($values['modificator_id']))
        throw new Exception("Need the modificator_id");
      $values['modification_date'] = new Zend_Date(null);
      parent::updateWithValues($values);
    }


		public function validateTitle()
		{
		  if (empty($this->_title))
		    return T_('Title can\'t be empty');
		  return null;
		}

    public function getCreator()
    {
      if (empty($this->_creatorId))
        return null;
      if ($this->_creator === null || $this->_creatorId !== $this->_creator->users_id)
      {
        $table = new USVN_Db_Table_Users();
        $this->_creator = $table->fetchRow(array('users_id = ?' => $this->_creatorId));
      }
      return $this->_creator;
    }

    public function getModificator()
    {
      if (empty($this->_modificatorId))
        return null;
      if ($this->_modificator === null || $this->_modificatorId !== $this->_modificator->users_id)
      {
        $table = new USVN_Db_Table_Users();
        $this->_modificator = $table->fetchRow(array('users_id = ?' => $this->_modificatorId));
      }
      return $this->_modificator;
    }

		public function getMilestone()
		{
			if ($this->_milestoneId === null)
				return null;
			if ($this->_milestone === null)
				$this->_milestone = Default_Model_Milestone::find($this->_milestoneId);
			return $this->_milestone;
		}

		static public function types()
		{
		  return array(
		    0 => 'new feature',
		    1 => 'improvement',
		    2 => 'bug',
		    3 => 'other');
		}

		public function getTypeText()
		{
		  $t = self::types();
		  if (array_key_exists($this->_type, $t))
		    return $t[$this->_type];
		  return $this->_type;
		}

		public function getPriorityImage()
		{
		  $imgs = array(
		    2 => 'red-priority.png',
		    1 => 'yellow-priority.png',
		    0 => 'green-priority.png',
		    -1 => 'blue-priority.png'
		    );
		    if (array_key_exists($this->_priority, $imgs))
		      return $imgs[$this->_priority];
		    return 'blue-priority.png';
		}

    static public function priorities()
    {
      return array(
        2 => 'Urgent',
        1 => 'Important',
        0 => 'Normal',
        -1 => 'Secondary'
        );
    }
		
		public function getPriorityText()
		{
		  $p = self::priorities();
		  if (array_key_exists($this->_priority, $p))
		    return $p[$this->_priority];
		  return $this->_priority;
		}

    public function getStatusText()
    {
      $s = self::statuses();
      if (array_key_exists($this->_status, $s))
        return $s[$this->_status];
      return $this->_status;
    }

    static public function statuses()
    {
      return array(
        0 => T_('Open'),
        1 => T_('Resolved'),
        2 => T_('Close')
        );
    }

    public function getAssignedTo()
    {
      if (empty($this->_assignedToId))
        return null;
      if ($this->_assignedTo === null || $this->_assignedToId !== $this->_assignedTo->users_id)
      {
        $table = new USVN_Db_Table_Users();
        $this->_assignedTo = $table->fetchRow(array('users_id = ?' => $this->_assignedToId));
      }
    	return $this->_assignedTo;
    }
}
