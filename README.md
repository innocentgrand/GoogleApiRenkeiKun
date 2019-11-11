# GoogleApiRenkeiKun

ここには、TranslateApiv3をPHPで叩こうとしたらまだライブラリがなくて
悲しい思いをしたために作った機能があります。

Here is a function that I made because I felt sad that there was no library yet when I tried to hit TranslateApiv3 with PHP.

## Description
正直すべての機能が必要でしょうか？きっとすべての機能はいらないはずですし、もっと簡単な最低限度のものでもいいんじゃないかと思って作り始めました。
現在「翻訳」だけに対応していますが、GCPのストレージなどにも対応しようと思います。

Honestly do you need all the features? I&#39;m sure I don&#39;t need all the functions, and I started making it because I thought it would be nicer and simpler. Currently only &quot;translation&quot; is supported, but I would like to support GCP storage.

## Demo
~~~~
namespace Innocentgrand\GoogleApiRenkeiKun;
require_once dirname(__DIR__) . '/vendor/autoload.php';

$a = new GoogleTranslate("/home/innocentgrand/TranslaterAPIProject-5f7ed7c844f8.json");

$text = <<<TEXT
日本語。
TEXT;

var_dump($a->translation($text));
~~~~
