<div id="top"></div>

## 使用技術一覧

<!-- シールド一覧 -->
<!-- 該当するプロジェクトの中から任意のものを選ぶ-->
<p style="display: inline">
  <!-- フロントエンドのフレームワーク一覧 -->
  <img src="https://img.shields.io/badge/-Vue.js-4FC08D.svg?logo=vue.js&style=for-the-badge">
  <!-- バックエンドのフレームワーク一覧 -->
  <img src="https://img.shields.io/badge/-Laravel-E74430.svg?logo=laravel&style=for-the-badge">
  <!-- バックエンドの言語一覧 -->
  <img src="https://img.shields.io/badge/-Php-777BB4.svg?logo=php&style=for-the-badge">
  <!-- ミドルウェア一覧 -->
  <img src="https://img.shields.io/badge/-MySQL-4479A1.svg?logo=mysql&style=for-the-badge&logoColor=white">
  <!-- インフラ一覧 -->
  <img src="https://img.shields.io/badge/-Docker-1488C6.svg?logo=docker&style=for-the-badge">
  <img src="https://img.shields.io/badge/-Github-181717.svg?logo=github&style=for-the-badge">
  <img src="https://img.shields.io/badge/-Heroku-430098.svg?logo=heroku&style=for-the-badge">
</p>

## サービス概要
### サービス名
シフト管理アプリ  
URLは現在非公開です  

### 概要
アルバイト先で使うシフトを管理、作成するアプリです

## 使い方

### 新規登録
管理者の方のみ新規登録ができます。  
<img alt="signup" src="https://gyazo.com/2610594408d5fd5b804fda573ba303d0.png" width="60%">

### ホーム画面
公開された自分のシフトが表示されます。  
<img alt="home" src="https://gyazo.com/15e06c1a0ff8a3a8e355900b7dc2b72e.gif" width="60%">

### 希望シフト作成
自分の入りたいシフトを追加したり削除することができます。  
<img alt="shift_hope_0" src="https://gyazo.com/e38b8e28fb04c9e7ab5c46f98c1f04b1.gif" width="60%">
<img alt="shift_hope_1" src="https://gyazo.com/e2a60c095ada215cf8efd3b3112b78cc.gif" width="60%">  

シフトは曜日ごとに一括で追加もできます  
<img alt="shift_hope_2" src="https://gyazo.com/28e94eebdd05b518cd5164e94e49eec6.gif" width="60%">

### 従業員管理
管理者の方のみ、使用できる機能で、同じ店舗の従業員の追加や削除を行うことができます。  
追加された従業員は、管理者ではなく一般ユーザーと認識されます。  
<img alt="shift_hope" src="https://gyazo.com/e29013eb06f1cb21326d22ba812b113d.png" width="60%">

### 日付ごとのシフト必要人数の設定
管理者の方のみ、使用できる機能で、勤務に必要な人数を日付けごとに設定できます。  
<img alt="shift_confirm_2" src="https://gyazo.com/4585ec6ed822e244392a3cce56fcdbfd.gif" width="60%"> 

### シフト管理
管理者の方のみ、使用できる機能で、従業員の希望シフトからシフトを作成し公開することができます。  
<img alt="shift_confirm_1" src="https://gyazo.com/7f41c100ad857e6c1b015293ebfaf8fb.png" width="60%">
<img alt="shift_confirm_2" src="https://gyazo.com/e95abca89c35b37239f8444829f742fb.png" width="60%">

## 苦労した点
シフト作成の際、早番・遅番の勤務可能人数を表示する機能の作成に苦労しました。  
以下の画像は、完成品のものですが、はっきりと勤務可能人数が表示されています。  
<img alt="shift_confirm_2" src="https://gyazo.com/c2a7de670f1345557d83d89fbca1abaa.png" width="30%">
  
当初、勤務可能人数は背景にテキストとして表示しようと考えていました。  
しかし、この方法で勤務可能人数を表示すると   
<img alt="shift_confirm_2" src="https://gyazo.com/d61b6e693d02cd79955d3cf4afd1a23d.png" width="30%">   
このように、文字が薄くなってしまうという問題がありました。（ちなみに、文字の色は黒色に設定しています。）  

この薄くなる問題は、どうやらアプリに使用しているライブラリの仕様上の問題のようでした。  
[textColor is not applied to background events #5528](https://github.com/fullcalendar/fullcalendar/issues/5528)  
色が薄いと視認性が低下するため、他の方法で表示しようと考えました。  

いろいろと考えたのですが、最終的には、勤務可能人数をイベントとして追加して表示させることにしました。  

イベントとは、カレンダーに追加したり削除したりできる予定のことです。  
<img alt="shift_confirm_2" src="https://gyazo.com/3ccdeedd78fc8a6e7b048610187cd393.png" width="30%">  

勤務可能人数をイベントとして追加して表示させると以下のようになります。  
<img alt="shift_confirm_2" src="https://gyazo.com/48dfc4030bfefab8de11976196d7b227.png" width="30%">  

そして、このイベントの背景の色を白色にすると以下のようになります。  
<img alt="shift_confirm_2" src="https://gyazo.com/398805325230c5e33015dee085ec9311.png" width="30%">  

以上の方法で、薄かった文字をはっきりと表示できるようになりました。  


## ER図
<img alt="shift_confirm_2" src="https://gyazo.com/e672ff04f65afab23143e495d091179e.png" width="60%">