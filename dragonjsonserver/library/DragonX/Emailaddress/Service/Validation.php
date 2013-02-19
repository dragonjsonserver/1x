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
 * Serviceklasse zur Verarbeitung von Validierungsabfragen
 */
class DragonX_Emailaddress_Service_Validation
{
    /**
     * Ändert die E-Mail Adresse trägt eine neue Validierungabfrage ein
     * @param string $newemailaddress
     * @dragonx_account_authenticate
     */
    public function changeEmailaddress($newemailaddress)
    {
        $logicValidation = new DragonX_Emailaddress_Logic_Validation();
        $configValidation = new Dragon_Application_Config('dragonx/emailaddress/validation');
        $logicValidation->changeEmailaddress(Zend_Registry::get('recordAccount'), $newemailaddress, $configValidation->validationhash, $configValidation->hashmethod);
    }

    /**
     * Validiert einen Account mit dem Hash der Validierungsabfrage
     * @param string $validationhash
     */
    public function validateEmailaddress($validationhash)
    {
        $logicValidation = new DragonX_Emailaddress_Logic_Validation();
        $logicValidation->validate($validationhash);
    }
}
