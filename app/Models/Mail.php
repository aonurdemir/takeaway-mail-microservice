<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string from
 * @property string to
 * @property string subject
 * @property string content
 * @property string state
 * @property string sender_third_party_provider_name
 * @property int    id
 */
class Mail extends Model
{
    use HasFactory;

    protected $table = 'mails';

    public const STATE_CREATED    = 'created';
    public const STATE_PROCESSING = 'processing';
    public const STATE_SENT       = 'sent';
    public const STATE_FAILED     = 'failed';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'from',
        'to',
        'subject',
        'content',
        'state',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function setAsSent()
    {
        $this->state = Mail::STATE_SENT;
    }

    public function isSent()
    {
        return $this->state === Mail::STATE_SENT;
    }

    public function setSenderThirdPartyProviderName($name)
    {
        $this->sender_third_party_provider_name = $name;
    }

    public function setAsFailed()
    {
        $this->state = Mail::STATE_FAILED;
    }

}
