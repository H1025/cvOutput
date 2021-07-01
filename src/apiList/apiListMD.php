<?php

namespace cvOutput\apiList;

use InvalidArgumentException;

class apiListMD
{
    private string $listTmpl;
    private string $infoTmpl;
    private string $listText = '';
    private string $infoText = '';

    public function __construct()
    {
        $this->listTmpl = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'apiList.tmpl');
        $this->infoTmpl = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'apiInfo.tmpl');
    }

    public function create(array $data)
    {
        $this->directionExist($data);

        // リスト作成
        $this->listText .= sprintf(
            "|[%s](#%s)|%s|   \n",
            $data['apiName'],
            str_replace('/', '', $data['apiName']),
            $data['request']['dump']['comment']['description']
        );

        // 出力用データ整形
        $this->infoText .= $this->infoTmpl;
        $this->createInfo('request', $data);
        $this->createInfo('response', $data);

        $this->infoText = str_replace(
            ['@name@', '@url@'],
            [
                $data['apiName'],
                $data['apiName']
            ],
            $this->infoText
        );
    }

    /**
     * 一覧と詳細を合わせて出力する
     *
     * @param string $outputPath
     * @return void
     */
    public function output(string $outputPath)
    {
        $text = str_replace(
            ['@list@', '@info@'],
            [
                $this->listText,
                $this->infoText
            ],
            $this->listTmpl
        );

        $fp = fopen($outputPath . DIRECTORY_SEPARATOR . 'README.md', 'w');
        fwrite($fp, $text);
        fclose($fp);
    }

    /**
     * RequestとResponseのデータが存在するかどうか
     *
     * @param array $data
     * @return void
     */
    private function directionExist(array $data)
    {
        if (!array_key_exists('response', $data)) {
            throw new InvalidArgumentException($data['apiName'] . ": response file not found ");
        }

        if (!array_key_exists('request', $data)) {
            throw new InvalidArgumentException($data['apiName'] . ": request file not found ");
        }
    }

    /**
     * キーの詳細文作成
     *
     * @param string $direction requestかresponse
     * @param array $data
     * @return void
     */
    private function createInfo(string $direction, array $data)
    {
        $apiData = $this->createDisplay($data[$direction]['dump']);

        $this->infoText = str_replace(
            [
                "@$direction" . 'ClassName@',
                "@$direction@",
                "@$direction" . 'Info@'
            ],
            [
                $data[$direction]['className'],
                $this->jsonEncode($apiData['sample']),
                $this->toTableMD($apiData['param'])
            ],
            $this->infoText
        );
    }

    /**
     * 表示するデータの作成
     *
     * @param array $data 元になるデータ
     * @param integer $count createDisplay内でcreateDisplayを使用する時のみ指定
     * @return array [
     *      sample => サンプル(jsonにする前の配列
     *      param => [
     *          lineNo => 行, name => キー名, type => 型, comment => 値の説明
     *      ]
     * ]
     */
    private function createDisplay(array $data, int &$count = 1): array
    {
        $isTop = ($count === 1);
        $keys = [
            'sample' => [],
            'param' => [],
            'count' => $count,
        ];

        $endValue = is_array($data['key']) ? array_key_last($data['key']) : '';

        foreach (array_keys($data['key']) as $value) {
            $valueData = $data['key'][$value];
            $count++;

            if (!empty($valueData['key'])) {
                $keyGet = $this->createDisplay($valueData, $count);

                $keys['sample'][$value] = $keyGet['sample'];

                // /api 以外の値は出力しない
                if (!$isTop || $value === 'api') {
                    $keys['param'] += $keyGet['param'];
                }
            } elseif (!empty($valueData['rule']['key'])) {
                $count++;
                $keyGet = $this->createDisplay($valueData['rule'], $count);
                $count++;
                $keys['sample'][$value][] = $keyGet['sample'];

                if (!$isTop || $value === 'api') {
                    $keys['param'] += $keyGet['param'];
                }
            } elseif ($valueData['type'] === 'list' && in_array($valueData['rule']['type'], ['int', 'string', 'float', 'bool'])) {
                $keys['sample'][$value] = $valueData['comment']['example'];
                $keys['param'][$count] = [
                    'lineNo' => $count,
                    'name' => $value,
                    'type' => $valueData['rule']['type'] . '[]',
                    'comment' => $valueData['comment']['description'],
                ];
            } else {
                $keys['sample'][$value] = $valueData['comment']['example'];

                $keys['param'][$count] = [
                    'lineNo' => $count,
                    'name' => $value,
                    'type' => $valueData['type'],
                    'comment' => $valueData['comment']['description'],
                ];
            }

            if ($endValue === $value)
                $count++;
        }

        return $keys;
    }

    /**
     * 渡された配列をjson文字列にエンコード
     * 先頭に4行の行番号を追加
     *
     * @param array $array
     * @return string
     */
    private function jsonEncode(array $array): string
    {
        $json = json_encode($array, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);

        $lines = [];
        $lineNo = 0;
        foreach (explode("\n", $json) as $line) {
            $lines[] = sprintf('%4d|', ++$lineNo) . $line;
        }
        $json = implode("\n", $lines);
        return $json;
    }

    /**
     * 配列をマークダウンの表化する
     *
     * @param array $array
     * @return string
     */
    private function toTableMD(array $array): string
    {
        if (empty($array)) {
            return '';
        }
        $result = "|行|キー名|型|値の説明|\n|----|----|----|----|\n";
        foreach ($array as $row) {
            $result .= '|' . implode('|', $row) . "|\n";
        }
        return $result;
    }
}
