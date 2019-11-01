<?php

namespace BlackIT\PrivyID\Commands;

use BlackIT\PrivyID\Events\PrivyIDDocumentStatusChangedEvent;
use BlackIT\PrivyID\PrivyIDDocument;
use Illuminate\Console\Command;

class PrivyIDDocumentCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'privyid:document_check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checking active document status';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $documents = PrivyIDDocument::whereIn('document_status',['In Progress'])->where('next_activity','<=',date('Y-m-d H:i:s'))->get();

        foreach($documents as $document) {
            $this->checkDocument($document);
        }

        \Log::info('Job Document Check '.date('Y-m-d H:i:s'));
    }

    public function checkDocument($document) {
        /**
         * @var $document PrivyIDDocument
         */
        $document = PrivyIDDocument::where('codification',$document->codification)->first();

        if ($document == null)
            return null;

        $object = $document->privyiduploadable();

        $checkDocument = $object->statusDocument($document->token);

        if ($checkDocument) {
            $recipientStatuses = json_decode($checkDocument->status_recipients,true);

            $statusDocument = true;
            foreach ($recipientStatuses as $index => $recipientStatus) {
                if ($recipientStatus['signatoryStatus'] == 'In Progress') {
                    $statusDocument = false;
                    break;
                }
            }

            if ($statusDocument) {
                $document->document_status = 'Completed';
                event(new PrivyIDDocumentStatusChangedEvent($document));
            }
        }

        $next = new \DateTime(date('Y-m-d H:i:s'));

        switch($document->execute_count) {
            case 0 :
                $next = $next->modify('+2 minute');
                break;
            case 1 :
                $next = $next->modify('+10 minute');
                break;
            case 2 :
                $next = $next->modify('+30 minute');
                break;
            case 3 :
                $next = $next->modify('+90 minute');
                break;
            default :
                $next = $next->modify('+210 minute');
                break;
        }
        $document->execute_count += 1;
        $document->next_activity = $next->format('Y-m-d H:i:s');
        $document->save();
    }
}
