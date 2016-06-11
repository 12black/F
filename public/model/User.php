<?php
namespace App\model;
use ZhiBo\Model;
class User extends Model{
        public $validate = [
                'title|标题' => 'empty|number',
        ];
}

