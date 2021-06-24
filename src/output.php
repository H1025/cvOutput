<?php

namespace cvOutput;

use cvOutput\language\csharp;
use cvOutput\apiList\apiListMD;

use mihoshi\hashValidator\hashValidator;

class output
{
    // TODO ↓消す
    private string $inputPath;
    private string $outputPath;

    public function __construct(string $inputPath, string $outputPath)
    {
        $this->inputPath = realpath($inputPath) ?: '';
        $this->outputPath = realpath($outputPath) ?: '';
        $this->input();
    }

    // public function csharp()
    // {
    //     new csharp($this->inputPath, $this->outputPath, $this->data);
    // }

    // public function apiListMD()
    // {
    //     new apiListMD($this->inputPath, $this->outputPath, $this->data);
    // }

    /**
     * Undocumented function
     *
     * @param string $inputPath
     * @return void
     */
    private function input()
    {
        $cwd = getcwd();
        foreach ($this->find($cwd, []) as $file) {
            $data = (new hashValidator($file, 'yaml'))->dump();
            $className = str_replace([$this->inputPath, DIRECTORY_SEPARATOR, '.yml'], '', $file);

            new csharp($className, $this->outputPath, $data);
            // new apiListMD($file, $this->outputPath, $data);
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
}
