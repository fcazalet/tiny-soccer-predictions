<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FixtureApiSource extends Model
{
    protected $fillable = ['fixture_id', 'source', 'external_id'];
}