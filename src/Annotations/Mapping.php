<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 2019-04-26
 */

namespace Larants\Hydrate\Annotations;


/**
 * Class MappingAnnotation
 * @package App\Hydrate\Annotations
 * @Annotation
 */
final class Mapping
{
    /**
     * field name of source
     *
     * @var string
     */
    public $name;
}