<?php

namespace App\Models;

trait GrantTrait
{
    /**
     * <table_name>.<filed_name>
     * 查询白名单字段，主要用来定义关联表哪些字段可以用作查询条件，所有字段可以用*表示，例如category.*。未来可扩展
     * @var array
     */
//    public $queryWhiteList = [];  // 具体model类中定义

    /**
     * 判断是否为可查询字段
     *
     * @param string $fieldName 联表字段名
     * @param string $relation 表名
     * @return boolean
     */
    public function isInQueryWhiteList($fieldName, $relation)
    {
        if(!property_exists(get_class($this), 'queryWhiteList')) {
            // TODO: 如果没定义白名单，默认拦截？
            return false;
        }
        foreach ($this->queryWhiteList as $whiteItem) {
            $whiteItemArray = explode('.', $whiteItem);
            if (2 == count($whiteItemArray)
                && strtolower($relation) == strtolower($whiteItemArray[0])
                && ( '*' == $whiteItemArray[1] || strtolower($fieldName) == strtolower($whiteItemArray[1]))) {
                return true;
            }
        }
        return false;
    }
}
