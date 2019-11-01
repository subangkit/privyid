<?php
/**
 * Created by IntelliJ IDEA.
 * User: subangkit
 * Date: 2019-10-31
 * Time: 09:09
 */

namespace BlackIT\PrivyID;


class PrivyIDRecipientBuilder
{
    protected $recipients = [];

    public function addRecipient($privyID, $type, $enterpriseToken = null) {
        if (array_key_exists($privyID,$this->recipients))
            return false;

        $this->recipients[$privyID] = [
            'type' => $type,
            'enterpriseToken' => $enterpriseToken
        ];

        return true;
    }

    public function changeRecipient($privyID,$type, $enterpriseToken = null) {
        if (!array_key_exists($privyID,$this->recipients))
            return false;

        $this->recipients[$privyID] = [
            'type' => $type,
            'enterpriseToken' => $enterpriseToken
        ];

        $this->recipients = array_values($this->recipients);

        return true;
    }

    public function removeRecipient($privyID) {
        if (!array_key_exists($privyID,$this->recipients))
            return false;

        unset($this->recipients[$privyID]);

        return true;
    }

    public function output() {
        $array = [];
        $privyID = new PrivyID();

        foreach ($this->recipients as $recipientID => $recipientData) {
            array_push($array, [
                'privyId' => $recipientID,
                'type' => $recipientData['type'],
                'enterpriseToken' => $recipientData['enterpriseToken']
            ]);
        }

        return $array;
    }
}
