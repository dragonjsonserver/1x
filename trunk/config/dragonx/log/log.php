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
 * @var array
 */
return array(
    'eventitems' => array(
        'requestid' => null,
        'accountid' => null,
        'params' => null,
    ),
    'writers' => array(
        new Zend_Log_Writer_Db(
            Zend_Registry::get('Zend_Db_Adapter_Abstract'),
            'dragonx_log_logs',
            array(
                'priority' => 'priority',
                'message' => 'message',
                'requestid' => 'requestid',
                'accountid' => 'accountid',
                'params' => 'params',
            )
        ),
    ),
);
