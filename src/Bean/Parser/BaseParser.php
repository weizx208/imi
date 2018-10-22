<?php
namespace Imi\Bean\Parser;

use Imi\Util\Traits\TSingleton;

abstract class BaseParser implements IParser
{
    use TSingleton;

    /**
     * 注解目标-类
     */
    const TARGET_CLASS = 'class';

    /**
     * 注解目标-属性
     */
    const TARGET_PROPERTY = 'property';

    /**
     * 注解目标-方法
     */
    const TARGET_METHOD = 'method';

    /**
     * 数据
     * @var array
     */
    protected $data = [];

    private function __construct($data = [])
    {
        $this->data = $data;
    }
    
    /**
     * 获取数据
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * 是否子类作为单独实例
     * @return boolean
     */
    protected static function isChildClassSingleton()
    {
        return true;
    }
}