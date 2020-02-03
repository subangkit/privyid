<?php
/**
 * Created by PhpStorm.
 * User: subangkit
 * Date: 2019-01-25
 * Time: 16:41
 */

namespace BlackIT\PrivyID;

use Illuminate\Database\Eloquent\Model;

class PrivyIDDocument extends Model
{
    protected $fillable = [
        'title',
        'type',
        'owner',
        'documeent',
        'recipients',
        'token',
        'url',
        'privy_uploadable_id',
        'privy_uploadable_type',
        'document_response_json',
        'last_status_updated',
        'execute_count',
        'next_activity',
    ];
    protected $table = 'privyid_documents';

    /**
     * Get the owning privyiduploadable model.
     */
    public function privyiduploadable()
    {
        return $this->morphTo();
    }
    
    public function privy_uploadable()
    {
        return $this->morphTo();
    }
}
