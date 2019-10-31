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

    public function addRecipient($privyID, $type) {
        if (array_key_exists($privyID,$this->recipients))
            return false;

        $this->recipients[$privyID] = [
            'type' => $type
        ];

        return true;
    }

    public function changeRecipient($privyID,$type) {
        if (!array_key_exists($privyID,$this->recipients))
            return false;

        $this->recipients[$privyID] = [
            'type' => $type
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
                'enterpriseToken' => null
            ]);
        }

        return $array;
    }
}
