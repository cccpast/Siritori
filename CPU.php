<?php
/*
 * CPUクラス
 * replyメソッド 全角を扱うのでstrlen、substrではなく、mb_strlen、mb_substr関数を使用
 * 最初にユーザが送る文字が、アルファベット、数字が送られてもCPUの負けとなる
 * 意味不明な単語をユーザが入力しても条件を満たしてさえいれば、続行される
 */
class CPU {
	
	/* 辞書(ヂ、ヅ、－、小さい文字ではじまる3文字がない) */
	private $wordDict = array(
		"あじと","いるか","うさぎ","えいご","おくら",
		"かじき","きづち","くうき","けいと","こあら",
		"さしみ","したい","すいか","せかい","そうじ",
		"たいこ","ちばし","つばめ","てんぐ","とまと",
		"なめこ","にじる","ぬりえ","ねっと","のっく",
		"はまき","ひづけ","ふじみ","へたれ","ほーむ",
		"まにあ","みるく","むーす","めんこ","もっぷ",
		"やぶか","ゆうき","よっと",
		"らくだ","りんご","るーく","れーす","ろっく",
		"わさび","をわり",
		"がーど","ぎぷす","ぐりこ","げーむ","ごーる",
		"ざいこ","じっぷ","ずっく","ぜうす","ぞうり",
		"だいす","でんわ","どぐう",
		"ばーる","びーる","ぶーつ","べーる","ぼうぐ",
		"ぱっく","ぴあす","ぷーま","ぺすと","ぽっと",
		"アイス","イルカ","ウサギ","エイゴ","オルカ",
		"カイロ","キナコ","クイズ","ケープ","コアラ",
		"サシミ","シール","スイカ","セカイ","ソード",
		"タイル","チーズ","ツバメ","テング","トマト",
		"ナメコ","ニート","ヌリエ","ネスト","ノイズ",
		"ハーフ","ヒール","フミエ","ヘルプ","ホーム",
		"マック","ミルク","ムース","メンコ","モップ",
		"ヤード","ユウシ","ヨット",
		"ラッパ","リンゴ","ルーク","レース","ロック",
		"ワイロ","ヲワリ",
		"ガード","ギプス","グリコ","ゲーム","ゴール",
		"ザイコ","ジップ","ズック","ゼウス","ゾウリ",
		"ダイス","デンワ","ドッグ",
		"バール","ビール","ブーツ","ベール","ボウグ",
		"パック","ピアス","プーマ","ペスト","ポット"
	);
	
	/* CPU返答集 */
	private $cpu_replyWord = array(
			"しりとりになっていないので、あなたの負けです",
			"3文字ではないのであなたの負けです",
			"「ん」がついたので、あなたの負けです",
			"降伏したので、あなたの負けです",
			"私の負けです"
	);
	
	/* コンストラクタ(いくつインスタンス化されているかチェックするだけ) */
	public function __construct() {
		static $countCls;
		$countCls++;
		echo 'インスタンス化された個数'.$countCls;
	}
	
	/* 返答 */
	public function reply($user_word) {
		
		if (isset($_SESSION['cpu_endWord'])) {
			$this->rmSession();
		}
		
		// しりとりになっていれば通す
		$user_word = $this->strFirstCheck($user_word);
		if ($user_word === 'first') {
			if (isset($_SESSION['cpu_endWord'])) {
				unset($_SESSION['cpu_endWord']);
			}
			return $this->cpu_replyWord[0];
		}
		
		// 通ればそのままユーザが入力した単語がリターンされる
		$user_word = $this->strLenCheck($user_word);
		if($user_word === 'len') {
			if (isset($_SESSION['cpu_endWord'])) {
				unset($_SESSION['cpu_endWord']);
			}
			return $this->cpu_replyWord[1];
		}
		
		// 通れば末尾の文字をリターンされる
		$user_word = $this->strEndCheck($user_word);
		if ($user_word === 'end') {
			if (isset($_SESSION['cpu_endWord'])) {
				unset($_SESSION['cpu_endWord']);
			}
			return $this->cpu_replyWord[2];
		}
		
		// 辞書に単語があれば、その言葉を返す
		foreach ($this->wordDict as $word) {
			if ($user_word === mb_substr($word, 0, 1)) {
				$cpu_word = $word;
				return $cpu_word;
			}
		}
		
		// 辞書に単語がなかった
		return $this->cpu_replyWord[4];
	}
	
	/* CPUが最後に使った文字の末尾を切り出し、セッションに保持 */
	public function saveStrEnd($cpu_word) {
		$endWord = mb_substr($cpu_word, mb_strlen($cpu_word, 'utf-8') -1, 1);
		return $endWord;
	}
	
	/* ユーザが空文字を送った場合の返答 */
	public function surrenderReply() {
		return $this->cpu_replyWord[3];
	}
	
	/* 最初の文字が前回CPUが使った言葉の末尾と一致するかチェック */
	private function strFirstCheck($user_word) {
		$firstWord = mb_substr($user_word, 0, 1);
		
		if (isset($_SESSION['cpu_endWord']) && $_SESSION['cpu_endWord'] !== $firstWord) {
			return 'first';
		} else {
			return $user_word;
		}
	}
	
	/* ユーザが入力した単語の語尾が「ん」か「ン」かチェック */
	private function strEndCheck ($user_word) {
		$user_word = mb_substr($user_word, mb_strlen($user_word, 'utf-8') -1, 1);
		
		if ($user_word === 'ん' || $user_word === 'ン') {
			return 'end';
		} else {
			return $user_word;
		}
	}
	
	/* 文字数が3文字かチェック */
	private function strLenCheck ($user_word) {
		if (mb_strlen($user_word, 'utf-8') !== 3) {
			return 'len';
		} else {
			return $user_word;
		}
	}
	
	/* CPUのセッションを確認、綺麗にする */
	private function rmSession() {
		if ($_SESSION['cpu_endWord'] === 'す') {
			unset($_SESSION['cpu_endWord']);
		}
	}
	
}