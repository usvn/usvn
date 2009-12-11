<?php

class USVN_View_Helper_FormSelect extends Zend_View_Helper_FormSelect
{

	public function formSelect($name, $value = null, $attribs = null, $options = null, $listsep = "<br />\n", $autoCorrectOpt = true)
	{
		if ($autoCorrectOpt === true && !empty($value))
		{
			$options = (array)$options;
			if (is_array($value))
			{
				foreach ($value as $aValue)
				{
					if (!array_key_exists($aValue, $options))
					{
						$options[$aValue] = $aValue;
					}
				}
			}
			else if ($value !== null)
			{
				if (!array_key_exists($value, $options))
				{
					$options[$value] = $value;
				}
			}
		}
		return parent::formSelect($name, $value, $attribs, $options, $listsep);
	}
}
