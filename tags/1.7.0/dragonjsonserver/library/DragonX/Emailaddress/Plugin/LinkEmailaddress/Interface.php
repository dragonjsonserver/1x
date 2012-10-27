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
 * Plugins die nach der Verknüpfung einer E-Mail Adresse aufgerufen werden
 */
interface DragonX_Emailaddress_Plugin_LinkEmailaddress_Interface
{
    /**
     * Wird nach der Verknüpfung einer E-Mail Adresse aufgerufen
     * @param Application_Account_Record_Account $recordAccount
     * @param DragonX_Emailaddress_Record_Emailaddress $recordEmailaddress
     */
    public function linkEmailaddress(Application_Account_Record_Account $recordAccount, DragonX_Emailaddress_Record_Emailaddress $recordEmailaddress);
}
