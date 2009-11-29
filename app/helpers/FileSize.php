<?php
/**
 * @author     Martin Hujer
 */
class USVN_View_Helper_FileSize
{
    /**
     * Array of units available
     * 
     * @var array
     */
    protected $_units;

    /**
     * Construct
     *
     */
    public function __construct()
    {
        /**
         * @see Zend_Measure_Binary
         */
        require_once 'Zend/Measure/Binary.php';
        $m = new Zend_Measure_Binary(0);
        $this->_units = $units = $m->getConversionList();
    }

    /**
     * Formats filesize with specified precision
     *
     * @param integer $fileSize Filesize in bytes
     * @param integer $precision Precision
     * @param string $norm Which norm use - 'traditional' (1 KB = 2^10 B), 'si' (1 KB = 10^3 B), 'iec' (1 KiB = 2^10 B)
     * @param string $type Defined export type
     */
    public function fileSize($fileSize, $precision = 0, $norm = 'traditional', $type = null)
    {
        try {
            require_once 'Zend/Registry.php';
            $locale = Zend_Registry::get('Zend_Locale');
            require_once 'Zend/Locale.php';
            if (!$locale instanceof Zend_Locale) {
                require_once 'Zend/Exception.php';
                throw new Zend_Exception('Locale is not set correctly.');
            }
            $isLocaleSet = true;
        } catch (Zend_Exception $e) {
            $isLocaleSet = false;
            $locale = null;
        }
        
        
        if ($isLocaleSet) {
            /**
             * @see Zend_Locale_Math
             */
            require_once 'Zend/Locale/Format.php';
            //get localised input value 
            $fileSize = Zend_Locale_Format::getFloat($fileSize, array('locale' => $locale));    
        } else {
            $fileSize = floatval($fileSize);
        }
        
        $m = new Zend_Measure_Binary($fileSize, null, $locale);
        
        $m->setType('BYTE');
        
        if (null === $norm) {
            $norm = 'traditional';
        }
        
        if (isset($type)) {
            $m->setType($type);
        } elseif ($norm === 'traditional') {
            if ($fileSize >= $this->_getUnitSize('TERABYTE')) {
                $m->setType(Zend_Measure_Binary::TERABYTE);
            } else if ($fileSize >= $this->_getUnitSize('GIGABYTE')) {
                $m->setType(Zend_Measure_Binary::GIGABYTE);
            } else if ($fileSize >= $this->_getUnitSize('MEGABYTE')) {
                $m->setType(Zend_Measure_Binary::MEGABYTE);
            } else if ($fileSize >= $this->_getUnitSize('KILOBYTE')) {
                $m->setType(Zend_Measure_Binary::KILOBYTE);
            }
        } elseif ($norm === 'si') {
            if ($fileSize >= $this->_getUnitSize('TERABYTE_SI')) {
                $m->setType(Zend_Measure_Binary::TERABYTE_SI);
            } else if ($fileSize >= $this->_getUnitSize('GIGABYTE_SI')) {
                $m->setType(Zend_Measure_Binary::GIGABYTE_SI);
            } else if ($fileSize >= $this->_getUnitSize('MEGABYTE_SI')) {
                $m->setType(Zend_Measure_Binary::MEGABYTE_SI);
            } else if ($fileSize >= $this->_getUnitSize('KILOBYTE_SI')) {
                $m->setType(Zend_Measure_Binary::KILOBYTE_SI);
            }
        }  elseif ($norm === 'iec') {
            if ($fileSize >= $this->_getUnitSize('TEBIBYTE')) {
                $m->setType(Zend_Measure_Binary::TEBIBYTE);
            } else if ($fileSize >= $this->_getUnitSize('GIBIBYTE')) {
                $m->setType(Zend_Measure_Binary::GIBIBYTE);
            } else if ($fileSize >= $this->_getUnitSize('MEBIBYTE')) {
                $m->setType(Zend_Measure_Binary::MEBIBYTE);
            } else if ($fileSize >= $this->_getUnitSize('KIBIBYTE')) {
                $m->setType(Zend_Measure_Binary::KIBIBYTE);
            }
        }
        return $m->toString($precision);
    }

    /**
     * Get size of $unit in bytes
     * 
     * @param string $unit
     */
    protected function _getUnitSize($unit)
    {
        if (array_key_exists($unit, $this->_units)) {
            return $this->_units[$unit][0];
        }
        return 0;
    }
}