<?php

namespace BezhanSalleh\FilamentExceptions\Trace;

use Illuminate\Support\Arr;
use JetBrains\PhpStorm\Pure;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use SplFileObject;

class Frame
{
    protected ?array $attributes = [];

    protected array | CodeBlock | null $code = [];

    public function __construct(protected ?string $frame = '')
    {
        $this->extract();
    }

    public function extract(): ?array
    {
        preg_match('/#\d+\s([^:]+):?\s?(.*)/', $this->frame, $matches);
        $this->parseFileAndLine($matches[1]);
        $this->parseCall($matches[2]);
        $this->fetchCodeBlock();

        return $this->attributes;
    }

    public function parseFileAndLine($str)
    {
        if (str()->startsWith($str, '/')) {
            preg_match('/^([^(]+)\((\d+)\)/', $str, $matches);
            [, $this->attributes['file'], $this->attributes['line']] = $matches;
        } else {
            $this->attributes['name'] = $str;
        }
    }

    public function parseCall($str)
    {
        if (empty($str)) {
            return;
        }
        if (preg_match('/^[^(]+(->|::)/', $str, $m)) {
            preg_match('/([^:-]+)(?:->|::)([^(]+)\((.*)\)/', $str, $matches);
            $this->attributes['class'] = $matches[1];
            $this->attributes['method'] = $matches[2];
            $this->attributes['args'] = $this->extractArgs($matches[3]);
            if (str()->contains($matches[2], ['{closure}']) && Arr::get($this->attributes, 'name') == '[internal function]') {
                $this->attributes['name'] .= " $matches[1]->$matches[2]";
            }
            // class method call
        } else {
            preg_match('/([^(]+)\((.*)\)/', $str, $matches);
            $this->attributes['function'] = $matches[1];
            $this->attributes['args'] = $this->extractArgs($matches[2]);
        }
    }

    public function fetchCodeBlock()
    {
        $filename = Arr::get($this->attributes, 'file');
        $lineNo = Arr::get($this->attributes, 'line');
        $class = Arr::get($this->attributes, 'class');
        $method = Arr::get($this->attributes, 'method');
        if ((! $filename || ! $lineNo) && ($class && $method)) {
            if (! class_exists($class)) {
                return;
            }
            $classReflection = new ReflectionClass($class);
            $filename = $classReflection->getFileName();
            if (! $classReflection->hasMethod($method)) {
                return;
            }
            $methodReflection = $classReflection->getMethod($method);

            $lineNo = $methodReflection->getStartLine();
        }
        if (! $filename || ! $lineNo) {
            return;
        }

        try {
            $file = new SplFileObject($filename);
            $target = max(0, ($lineNo - (5 + 1)));
            $file->seek($target);

            $curLineNo = $target + 1;
            $line = $prefix = $suffix = '';

            while (! $file->eof()) {
                if ($curLineNo == $lineNo) {
                    $line .= $file->current();
                } elseif ($curLineNo < $lineNo) {
                    $prefix .= $file->current();
                } elseif ($curLineNo > $lineNo) {
                    $suffix .= $file->current();
                }
                $curLineNo++;
                if ($curLineNo > $lineNo + 5) {
                    break;
                }
                $file->next();
            }
            $this->code = new CodeBlock($target + 1, $line, $prefix, $suffix);
            $this->attributes['file'] = $filename;
            $this->attributes['line'] = $lineNo;
        } catch (RuntimeException) {
            return;
        }
    }

    #[Pure]
    public function getCodeBlock(): array | CodeBlock
    {
        if (empty($this->code)) {
            return new CodeBlock();
        }

        return $this->code ?: new CodeBlock();
    }

    public function method()
    {
        return Arr::get($this->attributes, 'method', Arr::get($this->attributes, 'function', ''));
    }

    /**
     * @throws ReflectionException
     */
    public function args(): array
    {
        if (empty($this->attributes['args'])) {
            return [];
        }
        $args = [];
        $names = $this->getParameterNames();
        foreach ($this->attributes['args'] as $key => $val) {
            $args[Arr::get($names, $key, "param$key")] = $val;
        }

        return $args;
    }

    /**
     * @throws ReflectionException
     */
    public function getParameterNames(): array
    {
        $names = [];
        $class = Arr::get($this->attributes, 'class');
        $method = Arr::get($this->attributes, 'method');
        if ($class && isset($method)) {
            $classReflection = new ReflectionClass($class);
            if (! $classReflection->hasMethod($method)) {
                return $names;
            }
            foreach ($classReflection->getMethod($method)->getParameters() as $reflection) {
                $names[] = $reflection->getName();
            }
        }

        return $names;
    }

    protected function extractArgs($args): array
    {
        if (empty($args)) {
            return [];
        }
        $args = explode(',', $args);

        return array_map('trim', $args);
    }

    public function line()
    {
        return Arr::get($this->attributes, 'line', 0);
    }

    public function __call($method, $arguments = [])
    {
        return Arr::get($this->attributes, $method, '');
    }

    public function __get($key)
    {
        return Arr::get($this->attributes, $key, '');
    }
}
