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

    public function uploadDocument($codification,$title, $type, $filepath, $recipients) {
        $document = new PrivyIDDocument();
        $document->title = $title;
        $document->type = $type;
        $document->codification = $codification;

        $privyID = new PrivyID();

        $document->owner = json_encode($privyID->getOwner());
        $document->document = $filepath;
        $document->recipients = json_encode($recipients);
        $uploadResponse = $privyID->uploadDocument($title, $type, $document->owner, $filepath,$document->recipients);

        if (is_array($uploadResponse)) {
            if (isset($uploadResponse['data'])) {
                $data = $uploadResponse['data'];
                $document->token = $data['docToken'];
                $document->url = $data['urlDocument'];
                $document->document_response_json = json_encode($uploadResponse);

                return $this->privyid_documents()->save($document);

            }
        }

        return null;

    }

    public function statusDocument($docToken) {
        $privyID = new PrivyID();
        $statusResponse = $privyID->getDocumentStatus($docToken);
        if (is_array($statusResponse)) {
            $document = PrivyIDDocument::where('token',$docToken)->first();
            if ($document) {
                $document->last_status_updated = date('Y-m-d H:i:s');
                $document->status_response_json = json_encode($statusResponse);
                if (isset($statusResponse['data'])) {
                    $data = $statusResponse['data'];
                    $document->status_recipients = json_encode($data['recipients']);
                }

                $document->execute_count = 0;
                $next = new \DateTime(date('Y-m-d H:i:s'));
                $next = $next->modify('+2 minute');
                $document->next_activity = $next->format('Y-m-d H:i:s');
                $document->save();

                return $document;
            }
        }

        return false;
    }
}
