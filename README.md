# Csharp

[hash validator](https://github.com/m-seikou/hashValidator) でのバリデーションルールをもとに同等のデータ構造を持つクラスのC#コードを生成させる。
namespaceやクラス名,extendなど固有の値を明示することになるため、hashRuleを拡張したルールを使用することで実現している。

# 制限事項

UnityでHTTP通信を行う際のインターフェースを規定することを主目的としており、これに準じた制約が含まれている

# 対応フォーマット
hash validatorはPHPでの連想配列,jsonファイル,yamlファイルに対応していたが、本ツールではyamlのみ対応する

# C# ファイル作成

## Yamlファイルの記法に関して

### ファイル名

inputPathはディレクトリ指定
指定したディレクトリ以下の、ファイル名規則に沿ったファイル全てを対象とする

ファイル名規則
「Request(Response).yml」

inputPathで指定したディレクトリの1つ下のパスから、ファイル名を含めないファイルのパスまでをAPI名にする

例
ファイル構成: hoge/hogehoge/fuga/fugafuga/Request.yml

inputPath: hoge/hogehoge
API名: fuga/fugafuga
