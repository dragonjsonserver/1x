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

require 'password.php';

/**
 * Record zur Speicherung der Verknüpfungsdaten zu einem Account
 */
class DragonX_Emailaddress_Record_Emailaddress extends DragonX_Storage_Record_CreatedModified_Abstract
{
    /**
     * @var integer
     */
    public $account_id;

    /**
     * @var string
     */
    public $emailaddress;

    /**
     * @var string
     */
    public $passwordhash;

    /**
     * Validiert und setzt die übergebene E-Mail Adresse
     * @param string $emailaddress
     * @throws InvalidArgumentException
     * @return DragonX_Emailaddress_Record_Emailaddress
     */
    public function validateEmailaddress($emailaddress)
    {
        $emailaddress = strtolower($emailaddress);
        $validatorEmailaddress = new Zend_Validate_EmailAddress();
        if (!$validatorEmailaddress->isValid($emailaddress)) {
            throw new InvalidArgumentException('invalid emailaddress');
        }
        $this->emailaddress = $emailaddress;
        return $this;
    }

    /**
     * Generiert aus dem Passwort einen Hash
     * @param string $password
     * @return DragonX_Emailaddress_Record_Emailaddress
     */
    public function hashPassword($password)
    {
        $this->passwordhash = password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Generiert aus dem Passwort einen Hash
     * @param string $password
     * @return boolean
     */
    public function verifyPassword($password)
    {
        return password_verify($password, $this->passwordhash);
    }
}
