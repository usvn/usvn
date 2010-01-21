<?php

class Default_Model_Milestone extends Default_Model_Abstract
{
  /* Relations */
  protected $_tickets = null;
  protected $_creator = null;
  protected $_modificator = null;

  protected function _initNew(array $values)
  {
    if (empty($values['creator_id']))
	    throw new Exception("Need the creator_id");
    $values['modificator_id'] = $values['creator_id'];
    $values['creation_date'] = new Zend_Date(null);
    $values['modification_date'] = new Zend_Date(null);
    parent::_initNew($values);
  }

  protected function _initWithRow(Zend_Db_Table_Row $row)
	{
	  parent::_initWithRow($row);
  }

  public function validateTitle()
  {
    if (empty($this->title))
      return T_('Title can\'t be empty');
    return null;
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

  public function setDueDate($date)
  {
    if ($date instanceof Zend_Date)
      $this->_dueDate = $date;
    else if (empty($date))
      $this->_dueDate = null;
    else if (($date = $this->dateValue($date)) !== null)
      $this->_dueDate = $date;
    else
    {
      $this->addErrorForCol('due_date', T_('Invalid format'));
    }
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

	public function getTickets()
	{
	  if ($this->_tickets === null)
	  {
	    $this->_tickets = Default_Model_Ticket::fetchAll(array("milestone_id = ?" => $this->getId()));
	  }
		return $this->_tickets;
	}
}
