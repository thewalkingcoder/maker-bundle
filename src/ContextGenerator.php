<?php

declare(strict_types=1);

namespace Twc\MakerBundle;

class ContextGenerator
{
    /**
     * @var array
     */
    private $configs;

    public function __construct($configs)
    {
        $this->configs = $configs;
    }

    public function classNameByContext(
        string $component,
        string $initialClassName,
        ?string $context,
        string $target = 'target'
    ) {
        $namespace = $this->getNamespace($component, $context, $target);
        if (empty($namespace)) {
            return $initialClassName;
        }

        return '\\' . rtrim($namespace, '\\') . '\\' . $initialClassName;
    }

    public function getNamespace(string $component, ?string $context, string $target): ?string
    {
        if (empty($context)) {
            return null;
        }

        if (array_key_exists($component, $this->configs) === false) {
            return null;
        }

        foreach ($this->configs[$component] as $param) {
            if ($param['context'] === $context) {
                return $param[$target];
            }
        }

        return null;
    }

    public function getDirTemplateByContext(string $dirDefault, ?string $context)
    {
        if (empty($context)) {
            return $dirDefault;
        }

        foreach ($this->configs[Support::CONTROLLER] as $param) {
            if ($param['context'] === $context) {
                return $this->getDirTemplate($param);
            }
        }

        return $dirDefault;
    }

    protected function getDirTemplate($param)
    {
        if (array_key_exists('dir', $param) && !is_null($param['dir'])) {
            return rtrim($param['dir'], '/');
        }

        return $param['context'];
    }
}
