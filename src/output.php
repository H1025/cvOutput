<?php

namespace cvOutput;

use cvOutput\language\csharp;
use cvOutput\apiList\apiListMD;
use InvalidArgumentException;
use mihoshi\hashValidator\hashValidator;

class output
{
    private array $apiData;

    /**
     * output constructor
     *
     * @param string $inputPath
     */
    public function __construct(string $inputPath)
    {
        $this->input($this->fileCheck($inputPath));
    }

    public function csharp(string $outputPath)
    {
        $csharp = new csharp();
        foreach ($this->apiData as $api) {
            foreach ($api as $data) {
                $csharp->output($outputPath, $data);
            }
        }
    }

    public function apiListMD(string $outputPath)
    {
        $apiListMD = new apiListMD();
        foreach ($this->apiData as $apiName => $data) {
            $data['apiName'] = $apiName;
            $apiListMD->create($data);
        }
        $apiListMD->output($this->fileCheck($outputPath));
    }

    /**
     * fileの情報を$dataとして格納
     *
     * @return void
     */
    public function input(string $inputPath)
    {
        foreach ($this->find($inputPath, []) as $file) {
            $filePath = str_replace($inputPath . DIRECTORY_SEPARATOR, '', $file);
            $className = str_replace([DIRECTORY_SEPARATOR, '.yml'], '', $filePath);
            $apiName = $this->getApiName($filePath);

            $direction = $this->getDirection($className);

            $this->apiData[$apiName][$direction]['filePath'] = $filePath;
            $this->apiData[$apiName][$direction]['className'] = $className;

            $this->apiData[$apiName][$direction]['dump'] = (new hashValidator($file, 'yaml'))->dump();
        }
    }

    /**
     * Undocumented function
     *
     * @param string $path
     * @param array $list
     * @return array
     */
    private function find(string $path, array $list): array
    {
        foreach (scandir($path, SCANDIR_SORT_ASCENDING) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            if (is_dir($path . DIRECTORY_SEPARATOR . $file)) {
                $list = $this->find($path . DIRECTORY_SEPARATOR . $file, $list);
                continue;
            }
            if (strpos($file, 'Request.yml') === false && strpos($file, 'Response.yml') === false) {
                continue;
            }
            $list[] = $path . DIRECTORY_SEPARATOR . $file;
        }

        return $list;
    }

    /**
     * RequestかResponseか
     *
     * @param string $file
     * @return string (request|response)
     */
    private function getDirection(string $file): string
    {
        if (strpos($file, 'Request')) {
            return 'request';
        } elseif (strpos($file, 'Response')) {
            return 'response';
        }
        throw new InvalidArgumentException();
    }

    /**
     * パス名からApi名取得
     *
     * @param string $file
     * @return string
     */
    private function getApiName(string $file): string
    {
        $file = strtolower($file);
        $file = preg_replace('@/(response|request)\.yml@', '', $file);
        return $file;
    }

    /**
     * ファイルの存在チェック
     * 絶対パスで返す
     *
     * @param string $path
     * @return string
     */
    private function fileCheck(string $path): string
    {
        $realpath = realpath($path);
        if(!$realpath)
        {
            throw new InvalidArgumentException($path);
        }

        return $realpath;
    }
}
