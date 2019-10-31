<?php
/**
 * Created by IntelliJ IDEA.
 * User: subangkit
 * Date: 2019-10-31
 * Time: 09:06
 */

namespace BlackIT\PrivyID;


trait PrivyIDUploadable
{
    public function privyid_documents()
    {
        return $this->morphMany(PrivyIDDocument::class, 'privy_uploadable');
    }

    public function privyid_document()
    {
        return $this->privyids()->orderBy('created_at','DESC')->first();
    }

    public function uploadDocument($title, $type, $filepath, $recipients) {
        $document = new PrivyIDDocument();
        $document->title = $title;
        $document->type = $type;

        $privyID = new PrivyID();

        $document->owner = json_encode($privyID->getOwner());
        $document->document = $filepath;
        $document->recipients = json_encode($recipients);
        $uploadResponse = $privyID->uploadDocument($title, $type, $document->owner, $filepath,$document->recipients);
        exit;

        return $this->privyid_documents()->save($document);
    }
}
