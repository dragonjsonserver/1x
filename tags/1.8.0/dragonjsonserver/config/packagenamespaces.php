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
 * @return array
 */
return array(
    'Dragon' => array(
        'Application' => array(
            'Service' => array('Application'),
        ),
        'Json',
        'Package',
        'Plugin',
        'Repository',
    ),
    'DragonX' => array(
        'Account' => array(
            'Plugin' => array('Account', 'Deletion', 'Session', 'Install_Deletion', 'Install_Session'),
            'Service' => array('Account', 'Deletion', 'Session'),
        ),
        'Acl' => array(
            'Plugin' => array('Acl', 'Install_Accountrole', 'Install_Resource', 'Install_Role', 'Install_Roleresource'),
        ),
        'Application',
        'Clientmessage' => array(
            'Plugin' => array('Clientmessage', 'Account', 'All', 'Install_Account', 'Install_All'),
        ),
        'Cronjob' => array(
            'Plugin' => array('Install_Cronjob'),
            'Service' => array('Cronjob'),
        ),
        'Device' => array(
            'Plugin' => array('Install_Device'),
            'Service' => array('Device'),
        ),
        'Emailaddress' => array(
            'Plugin' => array('Install_Credential', 'Install_Emailaddress', 'Install_Validation'),
            'Service' => array('Credential', 'Emailaddress', 'Validation'),
        ),
        'Homepage',
        'Log' => array(
            'Plugin' => array('Log', 'Request', 'Install_Log', 'Install_Request'),
        ),
        'NestedSet',
        'Storage' => array(
            'Plugin' => array('Database', 'Storage'),
        ),
    ),
    'Application' => array(
        'Account' => array(
            'Plugin' => array('Install_Account'),
        ),
    ),
);
