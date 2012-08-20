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
abstract class Dragon_Application_Accessor
{
    /**
     * Nimmt ein Array oder eine andere Eigenschaft als Datenquelle an
     * @param array|Dragon_Application_Accessor $data
     */
    public function __construct($data = array())
    {
        if ($data instanceof Dragon_Application_Accessor) {
            $data = $data->toArray();
        }
        $this->fromArray($data);
    }

    /**
     * Setzt alle Attribute der Eigenschaft aus den Daten des Arrays
     * @param array $data
     * @return Dragon_Application_Accessor
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
     * @return array
     */
    public function toArray()
    {
        $array = array();
        foreach ($this as $key => $value) {
            if ($key[0] == '_') {
                $key = substr($key, 1);
            }
            try {
                $array[$key] = $this->__get($key);
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
        try {
            $reflectionProperty = new ReflectionProperty($this, $key);
            if ($reflectionProperty->isPublic()) {
                $this->$key = $value;
                return;
            }
        } catch (Exception $exception) {
        }
        $methodname = 'set' . ucfirst($key);
        if (!method_exists($this, $methodname)) {
            throw new InvalidArgumentException('missing attribute "' . $key . '"');
        }
        call_user_func(array($this, $methodname), $value);
    }

    /**
     * Gibt öffentliche Attribute direkt und Geschützte über den Getter zurück
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        try {
            $reflectionProperty = new ReflectionProperty($this, $key);
            if ($reflectionProperty->isPublic()) {
                return $this->$key;
            }
        } catch (Exception $exception) {
        }
        $methodname = 'get' . ucfirst($key);
        if (!method_exists($this, $methodname)) {
            throw new InvalidArgumentException('missing attribute "' . $key . '"');
        }
        return call_user_func(array($this, $methodname));
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
}
