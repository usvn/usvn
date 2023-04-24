<?php
/*
	Replacement Array class for ZendRegistry.
	This one uses ArrayAccess and doesn't busy loop with PHP8.1
*/
class Zend_Registry implements ArrayAccess
{
    private static $_instance = null;
	private $_values = array( );



    public function __construct( )
    {

    }

    public static function getInstance( )
    {
		if( self::$_instance === null )
		{
			$c = __CLASS__;
			self::$_instance = new $c;
		}
		
		return self::$_instance;
    }



	public static function get( $index )
    {
        $instance = self::getInstance( );
        if( !$instance->offsetExists( $index ) )
		{
            throw new Zend_Exception("No entry is registered for key '$index'");
        }
        return $instance[ $index ];
    }

    public static function set( $index, $value )
    {
        $instance = self::getInstance( );
        $instance[ $index ] = $value;
    }

    public static function isRegistered($index)
    {
		$instance = self::getInstance( );
        return $instance->offsetExists( $index );
    }



    public function offsetExists( $index )
    {
        return isset( $this->_values[ $index ] );
    }

// BEGIN For ArrayAccess
	public function offsetGet( $offset )
	{
		return $this->_values[ $offset ];
	}

	public function offsetSet( $offset, $value )
	{
		$this->_values[ $offset ] = $value;
	}

	public function offsetUnset( $offset )
	{
		unset( $this->_values[ $offset ] );
	} 
// END For ArrayAccess



}
