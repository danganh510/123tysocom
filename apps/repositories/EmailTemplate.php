<?php

namespace Bincg\Repositories;

use Bincg\Models\BinApply;
use Bincg\Models\BinContactus;
use Bincg\Models\BinTemplateEmail;
use Bincg\Models\BinUser;
use Phalcon\Mvc\User\Component;

/**
 * Class EmailTemplate
 * @property \GlobalVariable globalVariable
 * @package Bincg\Repositories
 */
class EmailTemplate extends Component {
    const EMAIL = 'EMAIL';
    const PDF = 'PDF';
    const logoUrl = 'frontend/images/logo.svg';
    const logoReceiptUrl = 'frontend/images/logo.png';
    public static function checkKeyword($emailtemplate_type, $emailtemplate_id)
    {
        return BinTemplateEmail::findFirst(
            array (
                'email_type = :type: AND email_id != :emailtemplateid:',
                'bind' => array('type' => $emailtemplate_type, 'emailtemplateid' => $emailtemplate_id),
            ));
    }

    public static function isTypePdf($type)
    {
        return $type == self::PDF;
    }

    public function findByTypeAndLanguage($emailType,$languageCode='en') {
        $email = false;
        if ($languageCode && $languageCode != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT te.*, tel.* FROM \Bincg\Models\BinTemplateEmail te
                INNER JOIN \Bincg\Models\BinTemplateEmailLang tel
                ON te.email_id = tel.email_id
                WHERE tel.email_lang_code = :lang:
                AND te.email_type = :type:";
            $item = $this->modelsManager->executeQuery($sql, array('lang' => $languageCode, 'type' => $emailType ))->getFirst();
            if ($item && sizeof($item) > 0) {
                $email = \Phalcon\Mvc\Model::cloneResult(
                    new BinTemplateEmail(),
                    array_merge($item->te->toArray(), $item->tel->toArray())
                );
            }
        }
        else {
            $email = BinTemplateEmail::findFirst(array(
                'email_type = :type:',
                'bind' => array('type' => $emailType),
            ));
        }
        return $email;
    }

    public function completeContentTemplate($content, $type = 'EMAIL', $languageCode='en') {
        $type = (!isset($type) || !is_string($type) || !in_array($type, array(self::EMAIL, self::PDF))) ? self::EMAIL : strtoupper(trim($type));

        if (self::isTypePdf($type)) {
            $headerMsg = '';
            $footerMsg = '';
            /*$header = BinTemplateEmail::findFirst(array(
                'email_type = :email_type: AND email_status = "Y"',
                'bind' => array('email_type' => 'PDF_HEADER')
            ));
            $footer = BinTemplateEmail::findFirst(array(
                'email_type = :email_type: AND email_status = "Y"',
                'bind' => array('email_type' => 'PDF_FOOTER')
            ));*/

            $headerAddress = self::findByTypeAndLanguage('PDF_HEADER_ADDRESS', $languageCode);
            if ($headerAddress)
                $content = str_replace("|||PDF_HEADER_ADDRESS|||", $headerAddress->getEmailContent(), $content);
            else
                $content = str_replace("|||PDF_HEADER_ADDRESS|||", "", $content);

            $footerAddress = self::findByTypeAndLanguage('PDF_FOOTER_ADDRESS', $languageCode);
            if ($footerAddress)
                $content = str_replace("|||PDF_FOOTER_ADDRESS|||", $footerAddress->getEmailContent(), $content);
            else
                $content = str_replace("|||PDF_FOOTER_ADDRESS|||", "", $content);

            $content = str_replace("|||LANG|||", '/'.$languageCode, $content);

            return $headerMsg . $content . $footerMsg;
        }
        else {
            $headerMsg = '';
            $header = self::findByTypeAndLanguage('EMAIL_HEADER', $languageCode);
            if ($header) {
                $headerMsg = $header->getEmailContent();
            }

            $footerMsg = '';
            $footer = self::findByTypeAndLanguage('EMAIL_FOOTER', $languageCode);
            if ($footer) {
                $footerMsg = $footer->getEmailContent();
            }

            $headerMsg = str_replace("|||LOGO_URL|||", $this->url->getStatic(self::logoUrl), $headerMsg);
            $content = str_replace("|||LANG|||", '/'.$languageCode, $content);

            return  $headerMsg . $content . $footerMsg;
        }
    }

    /**
     * @param \Bincg\Models\BinUser $user
     * @param $pass
     * @param $languageCode
     * @return array
     */
    public function getEmailNewUser($user, $pass,$languageCode)
    {
        $templateEmail = $this->findByTypeAndLanguage('EMAIL_CREATE_NEW_USER',$languageCode);

        $role = Role::getNameRole($user->getUserRoleId());
        if ($role == '') {
            $role = 'User';
        }
        if (!$templateEmail) return array('success' => false, 'content' => '');
        $user = BinUser::findFirstById($user->getUserId());
        $subject = $templateEmail->getEmailSubject();
        $content = $templateEmail->getEmailContent();
        $content = str_replace(array("|||USER_NAME|||", "|||USER_EMAIL|||", "|||USER_PASSWORD|||", "|||USER_ROLE|||"), array($user->getUserName(), $user->getUserEmail(), $pass, $role), $content);

        $content = $this->completeContentTemplate($content, 'EMAIL',$languageCode);

        return array('success' => true, 'subject' => $subject, 'content' => $content);
    }

    public function getEmailResetPass($user, $pass,$languageCode)
    {
        $templateEmail = $this->findByTypeAndLanguage('EMAIL_RESET_PASSWORD',$languageCode);

        if (!$templateEmail) return array('success' => false, 'content' => '');
        $user = BinUser::findFirstById($user->getUserId());
        $subject = $templateEmail->getEmailSubject();
        $content = $templateEmail->getEmailContent();
        $content = str_replace(array("|||USER_NAME|||", "|||USER_EMAIL|||", "|||USER_PASSWORD|||"), array($user->getUserName(), $user->getUserEmail(), $pass), $content);

        $content = $this->completeContentTemplate($content, 'EMAIL',$languageCode);

        return array('success' => true, 'subject' => $subject, 'content' => $content);
    }
    public function getEmailContactus($contactus,$lang_code)
    {
        $templateEmail = self::findByTypeAndLanguage('EMAIL_CONTACT_US',$lang_code);
        if (!$templateEmail) return array('success' => false, 'content' => '');
        $contactus = BinContactus::findFirstById($contactus->getContactId());
        //subject
        $subject = $templateEmail->getEmailSubject();
        //content
        $content = $templateEmail->getEmailContent();
        $content = str_replace(array("|||CONTACT_NAME|||","|||CONTACT_SUBJECT|||", "|||CONTACT_EMAIL|||", "|||CONTACT_PHONE|||", "|||CONTACT_CONTENT|||", "|||CONTACT_FILE|||","|||CONTACT_TOPIC|||","|||CONTACT_COMMUNICATION_CHANNEL|||"), array($contactus->getContactName(), $contactus->getContactSubject(),$contactus->getContactEmail(), $contactus->getContactNumber(), $contactus->getContactContent(), $contactus->getContactFile(),$contactus->getContactTopics(),$contactus->getContactCommunicationChannelName().': ['.$contactus->getContactCommunicationChannelNumber().']'), $content);
        $content = $this->completeContentTemplate($content, 'EMAIL', $lang_code);
        return array('success' => true, 'subject' => $subject, 'content' => $content);
    }
    /**
     * @param BinApply $apply
     * @return array
     */
    public function getEmailApplyCV($apply)
    {
        $templateEmail = self::findByTypeAndLanguage('EMAIL_APPLY_CV');
        if (!$templateEmail) return array('success' => false, 'content' => '');
        $apply = BinApply::findFirstById($apply->getApplyId());
        //content
        $content = $templateEmail->getEmailContent();
        $content = str_replace(array("|||APPLY_NAME|||", "|||APPLY_EMAIL|||", "|||APPLY_TEL|||", "|||APPLY_CAREER|||", "|||APPLY_FILE|||", "|||APPLY_CHANNEL_TYPE|||", "|||APPLY_CHANNEL_DETAIL|||"), array($apply->getApplyName(), $apply->getApplyEmail(), $apply->getApplyTel(), Career::getNameByID($apply->getApplyCareerId()), $apply->getApplyCv(), $apply->getApplyCommunicationChannelName(), $apply->getApplyCommunicationChannelNumber()), $content);
        $content = $this->completeContentTemplate($content, 'EMAIL');
        return array('success' => true, 'content' => $content);
    }

    public function getAllActiveEmailTemplateTranslate ($limit = null, $lang_code){
        $result = array();
        $sql = "SELECT * FROM Bincg\Models\BinTemplateEmail as e
                WHERE e.email_status = 'Y' AND e.email_id NOT IN 
                 (SELECT el.email_id FROM Bincg\Models\BinTemplateEmailLang as el WHERE el.email_lang_code =
                :lang_code:)";
        if (isset($limit) && is_numeric($limit) && $limit > 0) {
            $sql .= ' LIMIT '.$limit;
        }
        $lists = $this->modelsManager->executeQuery($sql,array('lang_code' => $lang_code));
        if(sizeof($lists)>0) $result = $lists;
        return $result;
    }
}
