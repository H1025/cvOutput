<?php

namespace cvOutput\apiList;

// ========================================
// 一旦ここの内容は保留
// ========================================
class apiListMD
{
    public function __construct(string $inputPath, string $outputPath, array $data)
    {
        $this->replacement($inputPath, $outputPath, $data);
    }

    private function replacement(string $inputPath, string $outputPath, array $data)
    {
        $listTmpl = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'apiList.tmpl');
        $infoTmpl = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'apiInfo.tmpl');
        // var_export($data);

        $apiName = $this->getApiName($inputPath);
        // var_dump($apiName);
        $text = str_replace(['@list@', '@info@'],
            [
                '',
                $infoTmpl
            ],
        $listTmpl);

        $fp = fopen($outputPath . DIRECTORY_SEPARATOR . 'README.md', 'w');
        fwrite($fp, $text);
        fclose($fp);
    }    

    private function createAPIListMD(string $outputFile, array $apiData): bool
    {
        $md = "# API一覧\n| API名 | 機能概要 |\n|----|----|\n";
        // 目次作成
        foreach ($apiData as $name => $data) {
            $name = $data['name'];
            $link = str_replace('/', '', $data['name']);
            $comment = preg_replace('/\r?\n/', '<br>', $data['comment']);
            $md .= sprintf("|[%s](#%s)|%s|\n", $name, $link, $comment);
        }
        $md .= "
API以下のみ詳細を記述<br>
その他の全API共通仕様に関しては[共通仕様(Common.md)](Common.md)参照
";
        foreach ($apiData as $name => $data) {
            $requestSample = $this->jsonEncode($data['request']['sample']);
            $responseSample = $this->jsonEncode($data['response']['sample']);
            $requestTable = $this->toTableMD($data['request']['param']);
            $responseTable = $this->toTableMD($data['response']['param']);
            $md .= sprintf('
# %s
## URL
>http://xxx.xxxx.xxx.xxx/%s
## Request
### Class
`%s`
### Key
```
%s
```
%s
## Responce
### Class
`%s`
### Key
```
%s
```
%s
<br>
', $name, $name, $data['request']['className'], $requestSample, $requestTable, $data['response']['className'], $responseSample, $responseTable);
        }
        return file_put_contents($outputFile, $md);
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
        $result = '|行|キー名|型|値の説明|
|----|----|----|----|
';
        foreach ($array as $row) {
            $result .= '|' . implode('|', $row) . "|\n";
        }
        return $result;
    }

    private function getApiName(string $file): string
    {
        $file = strtolower($file);
        $file = preg_replace('@/(response|request)\.yml@', '', $file);
        return $file;
    }

    /**
     * @param string $file
     * @return string (request|response)
     */
    private function getDirection(string $file): string
    {
        if (strpos($file, 'Request.yml') === false) {
            return 'response';
        }
        return 'request';
    }
}
