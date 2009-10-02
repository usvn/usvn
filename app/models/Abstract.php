<?php

/**
* 
*/
class Default_Model_Abstract
{

  static public function getMapper()
  {
    $class = get_class($this);
    if ($class == 'Default_Model_Abstract')
      throw Exception('Default_Model_Abstract is an abstract class');
    if ($class::_mapper == null)
    {
      
    }
    return $class::_mapper;
  }

  public function __construct(array $values)
  {
  }
}
