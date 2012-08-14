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
 * Liste zur Verwaltung mehrere Records eines Storages
 */
class DragonX_Storage_RecordList extends ArrayObject
{
    /**
     * Schränkt die erlaubten Objekte für die RecordList ein
     * @param string $key
     * @param mixed $value
     */
    public function offsetSet($key, $value)
    {
        if (!$value instanceof DragonX_Storage_Record_Abstract
            &&
            !$value instanceof DragonX_Storage_RecordList) {
            throw new InvalidArgumentException('invalid record');
        }
        parent::offsetSet($key, $value);
    }

    /**
     * Gibt eine neue Liste gruppiert auf die Attribute zurück
     * @param string|array $groupby
     * @return DragonX_Storage_RecordList
     */
    public function groupBy($groupby)
    {
    	if (!is_array($groupby)) {
    		$groupby = array($groupby);
    	}
        $list = new DragonX_Storage_RecordList();
    	$attributename = array_shift($groupby);
    	foreach ($this as $record) {
    		$attribute = $record->$attributename;
    		if (!isset($list[$attribute])) {
    		    $list[$attribute] = new DragonX_Storage_RecordList();
    		}
            $list[$attribute][] = $record;
    	}
    	if (count($groupby) > 0) {
	    	foreach ($list as &$sublist) {
	    		$sublist = $sublist->groupBy($groupby);
	    	}
	    	unset($sublist);
    	}
    	return $list;
    }
}
