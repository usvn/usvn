<?php

class Default_Model_Ticket
{
    protected $_title;
    protected $_created;
    protected $_description;
    protected $_id;

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
        $this->_title = $row['title'];
        $this->_created = new Zend_Date($row['created']);
        $this->_description = $row['description'];
        $this->_id = $row['id'];
    }

    protected function _initWithRow(Zend_Db_Table_Row $row)
    {
        $this->_title = $row->title;
        $this->_created = new Zend_Date($row->created);
        $this->_description = $row->description;
        $this->_id = $row->id;
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

    public function setTitle($title)
    {
        $this->_title = $title;
        return $this;
    }

    public function getTitle()
    {
        return $this->_title;
    }

    public function setCreated($created)
    {
        $this->_created = $created;
        return $this;
    }

    public function getCreated()
    {
        return $this->_created;
    }

    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
    }

    public function getDescription()
    {
        return $this->_description;
    }

    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function save()
    {
        $this->getMapper()->save($this);
    }

    static public function find($id)
    {
        return self::getMapper()->find($id);
    }

    static public function fetchAll()
    {
        return self::getMapper()->fetchAll();
    }
}
