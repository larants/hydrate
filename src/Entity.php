<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 2018/11/15
 */

namespace Larants\Hydrate;

use Illuminate\Support\Arr;
use Larants\Hydrate\Contracts\EntityInterface;
use Larants\Hydrate\Exceptions\MethodNotFoundException;

/**
 * Class Entity
 * @package Leading\Lib\Hydrate
 */
class Entity implements EntityInterface
{

    /**
     * @var array
     */
    protected $original = [];


    /**
     * @param array $data
     * @return static
     */
    public static function instance(array $data)
    {
        $entity = new static();

        return $entity->toObject($data);
    }

    /**
     * array to this object
     *
     * @param array $data
     * @return static
     */
    public function toObject(array $data)
    {
        $this->original = $data;

        return $this->getReflection()->hydrate($data, $this);
    }


    /**
     * object to array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getReflection()->extract($this);
    }

    /**
     * @param null $key
     * @return array|mixed
     */
    public function getOriginal($key = null)
    {
        if ($key) {
            return Arr::get($this->original, $key);
        }

        return $this->original;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return isset($this->$name) ? $this->$name : null;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws MethodNotFoundException
     */
    public static function __callStatic($name, $arguments)
    {
        $instance = new static();
        if (method_exists($instance, $name)) {
            return $instance->{$name}($arguments);
        }

        throw new MethodNotFoundException('');
    }

    /**
     * @return Reflection
     */
    protected function getReflection()
    {
        return app(Reflection::class);
    }
}