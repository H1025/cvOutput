<?php

namespace cvOutput\language;

class csharp
{
    /**
     * @param string $inputPath
     * @param string $outputPath
     */
    public function __construct(string $className, string $outputPath, array $data)
    {
        $this->output($className, $outputPath, $data);
    }

    private function output(string $className, string $outputPath, array $data)
    {
        $apiName = str_replace(['Request', 'Response'], '', $className);
        $fp = fopen($outputPath . DIRECTORY_SEPARATOR . $className . '.cs', 'w');

        fwrite($fp, 'namespace ' . 'test' . PHP_EOL . '{');
        $this->export($fp, $data, $className, 1, false, $apiName);
        fwrite($fp, '}');

        fclose($fp);
    }

    const TYPE = [
        'string' => 'string',
        'float' => 'float',
        'bool' => 'bool',
        'int' => 'long',
    ];

    /**
     * Undocumented function
     *
     * @param [type] $fp
     * @param array $rule
     * @param string $class
     * @param integer $indent
     * @param boolean $addProp
     * @param string $command
     * @return void
     */
    private function export($fp, array $rule, string $class, int $indent, bool $addProp, string &$command)
    {
        switch ($rule['type']) {
            case 'class':
                //path
            case 'hash':
                $abstract = (isset($rule['abstract']) && $rule['abstract']) ? 'abstract ' : '';
                $extend = (isset($rule['extend']) && !empty($rule['extend'])) ? (' : ' . $rule['extend']) : '';
                fwrite(
                    $fp,
                    PHP_EOL .
                        $this->indent($indent) . '[System.Serializable]' . PHP_EOL .
                        $this->indent($indent) . $abstract . 'public class ' . ucfirst($class) . $extend . ' {' . PHP_EOL
                );
                foreach ($rule['key'] as $name => $r) {
                    if ($extend !== '' && $name !== 'api') {
                        continue;
                    }
                    $this->export($fp, $r, $name, $indent + 1, true, $command);
                }

                if ($rule['type'] === 'class') {
                    $commandText = strpos($command, 'abstract') !== false ?
                        $this->indent($indent + 1) . 'public virtual string command => "";' . PHP_EOL :
                        $this->indent($indent + 1) . 'public override string command => "' . $command . '";' . PHP_EOL;
                    fwrite($fp, $commandText);
                }

                fwrite($fp, str_repeat('    ', $indent) . '}' . PHP_EOL);
                if ($addProp) {
                    fwrite($fp, $this->indent($indent) . 'public ' . ucfirst($class) . ' ' . $class . ';' . PHP_EOL);
                }
                return ucfirst($class);
            case 'list':
                if ($rule['rule']['type'] === 'hash') {
                    $type = $this->export($fp, $rule['rule'], $class, $indent, false, $command);
                } else {
                    $type = $rule['rule']['type'];
                }
                fwrite($fp, $this->indent($indent) . 'public ' . $type . '[] ' . $class . ';' . PHP_EOL);
                return $type;
            case 'string':
            case 'float':
            case 'bool':
            case 'int':
            case 'enum':
            case 'func':
                fwrite($fp, $this->indent($indent) . '/*' . $rule['comment']['description'] . '*/' . PHP_EOL);
                fwrite($fp, $this->indent($indent) . 'public ' . self::TYPE[($rule['return'] ?? $rule['type'])] . ' ' . $class . ';' . PHP_EOL);
                return $rule['type'];
            default:
                fwrite($fp, $this->indent($indent) . '/*' . PHP_EOL);
                fwrite($fp, $this->indent($indent) . ' * Undefined type[' . $rule['type'] . '] ' . $class . PHP_EOL);
                fwrite($fp, $this->indent($indent) . ' */' . PHP_EOL);
                return '';
        }
    }

    /**
     * インデント用の空白文字列作成
     *
     * @param [type] $n
     * @return string
     */
    private function indent(int $n): string
    {
        return str_repeat('    ', $n);
    }
}
