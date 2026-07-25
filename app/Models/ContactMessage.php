<?php

namespace App\Models;

use App\Enums\ContactMessageStatus;
use App\Models\Concerns\LogsContentActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory, LogsContentActivity;

    protected $fillable = ['first_name', 'last_name', 'email', 'phone', 'subject', 'message', 'source_url', 'status', 'read_at'];

    protected $casts = ['status' => ContactMessageStatus::class, 'read_at' => 'datetime'];

    protected function fullName(): Attribute
    {
        return Attribute::get(fn (): string => trim($this->first_name.' '.$this->last_name));
    }

    public function scopeNew(Builder $query): Builder
    {
        return $query->where('status', ContactMessageStatus::New->value);
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }
}
