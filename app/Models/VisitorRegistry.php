<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorRegistry extends Model
{
    protected $table = 'visitor_registry';

    protected $fillable = ['clicks', 'topic_id'];
    public function topics()
    {
        return $this->belongsTo(Topic::calss);
    }
}
