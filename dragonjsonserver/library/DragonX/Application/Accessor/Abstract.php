<?php
/**
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled with this
 * package in the file LICENSE.txt. It is also available through the
 * world-wide-web at this URL: http://dragonjsonserver.de/license. If you did
 * not receive a copy of the license and are unable to obtain it through the
 * world-wide-web, please send an email to license@dragonjsonserver.de. So we
 * can send you a copy immediately.
 *
 * @copyright Copyright (c) 2012 DragonProjects (http://dragonprojects.de)
 * @license http://framework.zend.com/license/new-bsd New BSD License
 * @author Christoph Herrmann <developer@dragonjsonserver.de>
 */

/**
 * Abstrakte Klasse mit den Basismethoden einer Eigenschaft
 */
abstract class DragonX_Application_Accessor_Abstract
{
    /**
     * Cache für die Information über die public Attribute
     * @var array
     */
    private static $_reflectionproperties = array();

    /**
     * Nimmt ein Array oder eine andere Eigenschaft als Datenquelle an
     * @param array|DragonX_Application_Accessor_Abstract $data
     */
    public function __construct($data = array())
    {
        $classname = get_class($this);
        if (!isset(self::$_reflectionproperties[$classname])) {
            self::$_reflectionproperties[$classname] = array();
            $reflectionclass = new ReflectionClass($this);
            foreach ($reflectionclass->getProperties(ReflectionProperty::IS_PUBLIC) as $reflectionproperty) {
                self::$_reflectionproperties[$classname][$reflectionproperty->name] = true;
            }
        }
        if ($data instanceof DragonX_Application_Accessor_Abstract) {
            $data = $data->toArray();
        }
        $this->fromArray($data);
    }

    /**
     * Setzt alle Attribute der Eigenschaft aus den Daten des Arrays
     * @param array $data
     * @return DragonX_Application_Accessor_Abstract
     */
    public function fromArray(array $data)
    {
        foreach ($data as $key => $value) {
            try {
                $this->__set($key, $value);
            } catch (Exception $exception) {
            }
        }
        return $this;
    }

    /**
     * Gibt alle Attribute der Eigenschaft als Array zurück
     * @param boolean $subarrays
     * @return array
     */
    public function toArray($subarrays = true)
    {
        $array = array();
        foreach ($this as $key => $value) {
            if ($key[0] == '_') {
                $key = substr($key, 1);
            }
            try {
                $value = $this->__get($key);
                if (!$subarrays && is_array($value)) {
                    $function = function ($function, $key, array $value) {
                        $subarray = array();
                        foreach ($value as $subkey => $subvalue) {
                            if (is_array($subvalue)) {
                                $subarray += $function($function, $key . '_' . $subkey, $subvalue);
                            } else {
                                $subarray[$key . '_' . $subkey] = $subvalue;
                            }
                        }
                        return $subarray;
                    };
                    $array += $function($function, $key, $value);
                } else {
                    $array[$key] = $value;
                }
            } catch (Exception $exception) {
            }
        }
        return $array;
    }

    /**
     * Setzt öffentliche Attribute direkt und Geschützte über den Setter
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        if (isset(self::$_reflectionproperties[get_class($this)][$key])) {
            $this->$key = $value;
            return;
        }
        $array = explode('_', $key);
        $self = $this;
        while (count($array) > 1) {
            $key = array_shift($array);
            if (is_object($self) && isset($self->$key)) {
                $self = &$self->$key;
            } elseif (is_array($self) && isset($self[$key])) {
                $self = &$self[$key];
            } else {
                break;
            }
            $subkey = implode('_', $array);
            if (array_key_exists($subkey, $self)) {
                $self[$subkey] = $value;
                return;
            }
        }
        $methodname = 'set' . ucfirst($key);
        if (method_exists($this, $methodname)) {
            call_user_func(array($this, $methodname), $value);
        }
    }

    /**
     * Gibt öffentliche Attribute direkt und Geschützte über den Getter zurück
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (isset(self::$_reflectionproperties[get_class($this)][$key])) {
            return $this->$key;
        }
        $array = explode('_', $key);
        $self = $this;
        while (count($array) > 1) {
            $key = array_shift($array);
            if (isset($self->$key)) {
                $self = &$self->$key;
            } elseif (isset($self[$key])) {
                $self = &$self[$key];
            } else {
                break;
            }
            $subkey = implode('_', $array);
            if (array_key_exists($subkey, $self)) {
                return $self[$subkey];
            }
        }
        $methodname = 'get' . ucfirst($key);
        if (method_exists($this, $methodname)) {
            return call_user_func(array($this, $methodname));
        }
    }

    /**
     * Prüft die Existenz des Attributes direkt oder über den Getter
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        try {
            return $this->__get($key) != null;
        } catch (Exception $exception) {
        }
        return false;
    }

    /**
     * Setzt das Attribute auf NULL, entfernt es jedoch nicht aus dem Objekt
     * @param string $key
     */
    public function __unset($key)
    {
        if (isset($this->$key)) {
            $this->$key = null;
        }
        if (isset($this->{'_' . $key})) {
           $this->{'_' . $key} = null;
        }
    }
}
