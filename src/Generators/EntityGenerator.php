<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 2019-02-10
 */

namespace Larants\Hydrate\Generators;


use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;
use Larants\Hydrate\Exceptions\FileExistsException;

class EntityGenerator
{

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $filesystem;

    protected $composer;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * 解析后的name
     * @var array
     */
    protected $pNames = [];

    /**
     * EntityGenerator constructor.
     * @param Filesystem $filesystem
     * @param Composer $composer
     */
    public function __construct(Filesystem $filesystem, Composer $composer)
    {
        $this->filesystem = $filesystem;
        $this->composer = $composer;
    }

    /**
     * @return bool
     * @throws FileExistsException
     */
    public function run()
    {
        $path = $this->getClassFilePath();
        if ($this->filesystem->exists($path) && !$this->option('force')) {
            throw new FileExistsException($path);
        }
        if (!$this->filesystem->isDirectory($dir = dirname($path))) {
            $this->filesystem->makeDirectory($dir, 0777, true, true);
        }

        $this->filesystem->put($path, $this->getContents());
        $this->composer->dumpAutoloads();

        return true;
    }

    /**
     * @return string
     */
    public function getClassFilePath()
    {
        $pNames = $this->parseName();
        $pathName = implode('/', $pNames);

        return app()->path() . '/Entities/' . $pathName . 'Entity.php';
    }

    /**
     * 首字母大写（驼峰（单数））
     * orders/test_users -- ["Orders", "TestUser"]
     *
     * @return array
     */
    public function parseName()
    {
        if (!$this->pNames) {
            $name = $this->option('name');
            if (Str::contains($name, '\\')) {
                $name = str_replace('\\', '/', $name);
            }

            $names = explode('/', Str::singular($name));
            $pNames = [];
            foreach ($names as $name) {
                $pNames[] = Str::studly($name);
            }
            $this->pNames = $pNames;
        }

        return $this->pNames;
    }

    /**
     * orders/test_users -- TestUserEntity
     *
     * @return string
     */
    public function getClassName()
    {
        $pNames = $this->parseName();
        $name = Arr::last($pNames);

        return $name . 'Entity';
    }

    /**
     * orders/test_users -- App\\Entities\\Orders
     *
     * @return mixed
     */
    public function getClassNamespace()
    {
        $pNames = $this->parseName();
        array_pop($pNames);
        $pathName = implode('\\', $pNames);
        $classNamespace = app()->getNamespace() . 'Entities\\' . $pathName;

        return rtrim(str_replace(["\\", ' / '], '\\', $classNamespace), '\\');
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }


    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Determinte whether the given key exist in options array.
     *
     * @param string $key
     *
     * @return boolean
     */
    public function hasOption($key)
    {
        return array_key_exists($key, $this->options);
    }

    /**
     * Get value from options by given key.
     *
     * @param string $key
     * @param string|null $default
     *
     * @return string
     */
    public function getOption($key, $default = null)
    {
        if (!$this->hasOption($key)) {
            return $default;
        }

        return $this->options[$key] ?: $default;
    }

    /**
     * Helper method for "getOption".
     *
     * @param string $key
     * @param string|null $default
     *
     * @return string
     */
    public function option($key, $default = null)
    {
        return $this->getOption($key, $default);
    }

    /**
     * Get stub contents.
     *
     * @return mixed|string
     */
    protected function getContents()
    {
        $contents = file_get_contents($this->getPath());
        $replaces = [
            'classNamespace' => $this->getClassNamespace(),
            'className' => $this->getClassName()
        ];
        foreach ($replaces as $search => $replace) {
            $contents = str_replace('$' . $search . '$', $replace, $contents);
        }

        return $contents;
    }

    /**
     * @return string
     */
    protected function getPath()
    {
        return __DIR__ . '/entity.stub';
    }
}