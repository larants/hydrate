<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 2019-05-21
 */

namespace Larants\Hydrate\Contracts;


interface EntityInterface
{

    public static function instance(array $data);

    public function toObject(array $data);

    public function toArray();

    public function getOriginal($key = null);
}