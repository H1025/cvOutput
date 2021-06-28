<?php

namespace cvOutput;

use mihoshi\hashValidator\hashValidator;

/**
 * cvOutput\apiData
 * 
 * @property string $className
 * @property string $filePath
 * @property string $direction
 * @property array $dump [
 *      API名 => [
 *          name => "hogehoge",
 *          comment => コメント
 *          request => [
 *              className "",
 *              param => [],
 *              sample => []
 *          ],
 *          response => [
 *              className "",
 *              param => [],
 *              sample => []
 *          ],
 *          filePath => "hoge/hoge"
 *      ]
 * ]
 */
class apiData
{
    // クラス名
    public string $className;
    // ファイルパス
    public string $filePath;
    // request or response
    public string $direction;
    // 
    public array $dump;
    
    public function __construct(array $data)
    {
        foreach($data as $key => $value)
        {
            if (!property_exists($this, $key)) {
                continue;
            }
            $this->$key = $value;
        }
    }

    public function toArray(): array
    {
        $array = [
            'className' => $this->className,
            'filePath' => $this->filePath,
            'direction' => $this->direction,
            'dump' => $this->dump,
        ];

        return $array;
    }
}
