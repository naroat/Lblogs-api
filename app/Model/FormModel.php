<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FormModel extends Model
{
    protected $table = 'form';

    //定义为秒时间戳
    protected $dateFormat = 'U';


    //不需要记录created_at或updated_at
    //protected $timestamps = false;

    protected $casts = [
        'id' => 'string',   //把id返回字符串
    ];

    protected $hidden = ['is_on'];

    public function formElements()
    {
        return $this->hasMany(FormElementModel::class, 'form_id', 'id')->orderBy('sort', 'asc');
    }
}
