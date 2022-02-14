<?php

/**
 * 单例模式： 保证一个类只有一个对象，并提供访问它的全局访问点
 * 使用场景：当您想控制实例数目，节省系统资源的时候。
 */
class Singleton
{
    //私有实例属性，保存实例
    private static $instance;

    //私有化构造方法， 防止外部实例
    private function __construct() {}

    //私有克隆方法，防止克隆
    private function __clone() {}

    //对外使用的公共方法， 获取实例
    public static function getInstance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}