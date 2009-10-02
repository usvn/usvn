<?php

class Default_Model_Ticket
{
  	protected $_id;
		protected $_creation_date;
		protected $_creator_id;
		protected $_modification_date;
		protected $_modificator_id;
    protected $_title;
    protected $_description;

    static public function getMapper()
    {
			return Default_Model_TicketsMapper::getInstance();
    }

    public function __construct($row = null)
    {
			if ($row instanceof Zend_Db_Table_Row)
				$this->_initWithRow($row);
			else
				$this->_initNew($row);
		}

    protected function _initNew(array $values)
    {
			$this->_id = $values['ticket_id'];
			$this->_creation_date = new Zend_Date($values['creation_date']);
			$this->_creator_id = $values['creator_id'];
			$this->_modification_date = new Zend_Date($values['modification_date']);
			$this->_modificator_id = $values['modificator_id'];
			$this->_title = $values['title'];
			$this->_description = $values['description'];
    }

    protected function _initWithRow(Zend_Db_Table_Row $row)
		{
			$this->_id = $row->ticket_id;
			$this->_creation_date = new Zend_Date($row->creation_date);
			$this->_creator_id = $row->creator_id;
			$this->_modification_date = new Zend_Date($row->modification_date);
			$this->_modificator_id = $row->modificator_id;
			$this->_title = $row->title;
			$this->_description = $row->description;
    }

		public function __set($name, $value)
		{
			$setter = 'set' . ucfirst($name);
			if (!method_exists($this, $setter))
			{
				throw new Exception('Try to set an invalid property "' . $name . '" for class "' . get_class($this) . '"');
			}
			$this->$setter($value);
		}

		public function __get($name)
		{
			$getter = 'get' . ucfirst($name);
			if (('mapper' == $name) || !method_exists($this, $getter))
			{
				throw new Exception('Try to get an invalid property "' . $name . '" for class "' . get_class($this) . '"');
			}
			return $this->$getter();
		}

		public function getId()
		{
			return $this->_id;
		}

		public function setCreationDate($txt)
		{
			$this->_creation_date = (string) $txt;
			return $this;
		}

		public function getCreationDate()
		{
			return $this->_creation_date;
		}

		public function setCreatorId($txt)
		{
			$this->_creator_id = (string) $txt;
			return $this;
		}

		public function getCreatorId()
		{
			return $this->_creator_id;
		}

		public function setModificationDate($txt)
		{
			$this->_created = (string) $txt;
			return $this;
		}

		public function getModificationDate()
		{
			return $this->_created;
		}

		public function setModificatorId($txt)
		{
			$this->_modificator_id = (string) $txt;
			return $this;
		}

		public function getModificatorId()
		{
			return $this->_modificator_id;
		}

		public function setTitle($txt)
		{
			$this->_title = (string) $txt;
			return $this;
		}

		public function getTitle()
		{
			return $this->_title;
		}

		public function setDescription($txt)
		{
			$this->_description = (string) $txt;
			return $this;
		}

		public function getDescription()
		{
			return $this->_description;
		}

		public function save()
		{
			$this->getMapper()->save($this);
		}
		
		public function delete()
		{
			$this->getMapper()->delete($this);
		}

		static public function find($id)
		{
			return self::getMapper()->find($id);
		}

		static public function fetchAll($where = null, $order = null)
		{
			return self::getMapper()->fetchAll($where, $order);
		}
		
		static public function fetchRow($where = null, $order = null)
		{
			return self::getMapper()->fetchRow($where, $order);
		}
}
