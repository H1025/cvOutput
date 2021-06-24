# Csharp

メモ書き
inputPathはディレクトリ指定
指定したディレクトリ以下の、ファイル名規則に沿ったファイル全てを対象とする

ファイル名規則
「Request(Response).yml」

inputPathで指定したディレクトリの1つ下のパスから、ファイル名を含めないファイルのパスまでをAPI名にする

例
ファイル構成: hoge/hogehoge/fuga/fugafuga/Request.yml

inputPath: hoge/hogehoge
API名: fuga/fugafuga