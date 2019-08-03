<?php

namespace Larants\Hydrate;

use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * Class AnnotationReader
 * @package App\Hydrate
 */
class AnnotationReader
{
    /**
     * @return DoctrineAnnotationReader
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public static function instance()
    {
        AnnotationRegistry::registerFile(__DIR__ . '/Annotations/Mapping.php');

        return new DoctrineAnnotationReader();
    }
}