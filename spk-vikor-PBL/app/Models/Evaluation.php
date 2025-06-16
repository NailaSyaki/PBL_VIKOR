<?php
// app/Models/Evaluation.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluation extends Model
{
    use HasFactory;
    protected $fillable = ['alternative_id', 'criterion_id', 'value'];

    public function alternative(): BelongsTo
    {
        return $this->belongsTo(Alternative::class);
    }

    public function criterion(): BelongsTo
    {
        return $this->belongsTo(Criterion::class);
    }
}