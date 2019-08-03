<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 2018/11/15
 */

namespace Larants\Hydrate;

use Exception;
use Illuminate\Support\Facades\Log;
use Larants\Hydrate\Annotations\Mapping;
use Larants\Hydrate\Contracts\EntityInterface;
use Larants\Hydrate\Exceptions\InvalidArgumentException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionProperty;


/**
 * 字段映射，仅支持public属性
 *
 * Class Reflection
 * @package Leading\Lib\Hydrate
 */
class Reflection
{
    protected static $reflectProperties = [];

    /**
     * 将数组赋值到对象
     *
     * @param array $data
     * @param EntityInterface $object
     * @return mixed
     */
    public function hydrate(array $data, EntityInterface $object)
    {
        try {
            // 获取对象的属性
            $reflectProperties = $this->getReflectProperties($object);
            $annotationReader = AnnotationReader::instance();
            foreach ($reflectProperties as $name => $property) {
                // 获取默认值
                $defaultValue = $property->getValue($object);
                /* @var Mapping $column */
                $column = $annotationReader->getPropertyAnnotation($property, Mapping::class);
                if ($column instanceof Mapping) {
                    $value = Arr::get($data, $column->name, $defaultValue);
                } else {
                    $value = Arr::get($data, Str::snake($name), $defaultValue);
                }
                $reflectProperties[$property->name]->setValue($object, $value);
            }
        } catch (Exception $e) {
            Log::error('annotation reader error');
        }

        return $object;
    }


    /**
     * 对象转为数组
     *
     * @param $object
     * @return array
     */
    public function extract($object)
    {
        $result = [];
        try {
            foreach (self::getReflectProperties($object) as $property) {
                $value = $property->getValue($object);
                if (is_array($value)) {
                    $arrValue = [];
                    foreach ($value as $key => $item) {
                        if ($item instanceof EntityInterface) {
                            $arrValue[$key] = $this->extract($item);
                        }
                    }
                    $value = $arrValue;
                } elseif ($value instanceof EntityInterface) {
                    $value = $this->extract($value);
                }

                $name = Str::snake($property->getName());
                $result[$name] = $value;
            }
        } catch (Exception $e) {
            // todo 异常处理
            Log::error('object to array errors');
        }

        return $result;
    }

    /**
     * 获取要转化对象的属性
     *
     * @param $input
     * @return ReflectionProperty[]
     * @throws InvalidArgumentException
     * @throws \ReflectionException
     */
    protected function getReflectProperties($input)
    {
        if (is_object($input)) {
            $key = get_class($input);
        } else {
            throw new InvalidArgumentException('Input must be an object.');
        }

        if (isset(static::$reflectProperties[$key])) {
            return static::$reflectProperties[$key];
        }

        $reflectProperties = (new ReflectionClass($input))->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($reflectProperties as $property) {
            $property->setAccessible(true);
            static::$reflectProperties[$key][$property->getName()] = $property;
        }

        return static::$reflectProperties[$key];
    }
}