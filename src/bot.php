<?php 
ob_start();

define('API_KEY','5235900238:AAGbEzwXk-djRiucoOLHeAI1VRo7rJKtbVw');
function bot($method,$datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
       return $res;
    }
}

function run ($lang,$code){
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://rextester.com/rundotnet/Run');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "LanguageChoiceWrapper=$lang&Program=".urlencode($code));
return json_decode(curl_exec($ch),true);
curl_close($ch);
}

$update = json_decode(file_get_contents('php://input'));
if(isset($update->message)){
    $message = $update->message; 
    $chat_id = $message->chat->id;
    $text = $message->text;
}else
die();

$langs = ['php'=>8,'asm'=>15,'bash'=>38,'csharp'=>1,'cppgcc'=>7,'cgcc'=>6,'cclang'=>26,'clojure'=>47,'commonlisp'=>18,'d'=>30,'erlang'=>40,'fsharp'=>3,'go'=>20,'python'=>5,'perl'=>13, 'python3'=>24, 'r'=>31,'ruby'=>12, 'scala'=>21, 'scheme'=>22,'sql server'=>16,'swift'=>37,'tcl'=>32,'vb.net'=>2,'elixir'=>41,'erlang'=>40,'javascript'=>17, 'lisp'=>18, 'prolog'=>19, 'node.js'=>23, 'octave'=>25, 'MySQL'=>33, 'postgreSQL'=>34, 'oracle'=>35, 'ada'=>39, 'ocaml'=>42, 'kotlin'=>43, 'brainf***'=>44, 'fortran'=>45, 'rust'=>46, 'clojure'=>47];
$langtitle = ['php','asm','bash','csharp','cppgcc','cgcc','cclang','clojure','commonlisp','d','erlang','fsharp','go','python','perl','python3', 'r', 'ruby', 'scala', 'scheme', 'sql_server', 'swift', 'tcl', 'vb.net','elixir','erlang','javascript','lisp','prolog','node.js','octave', 'mysql', 'postgresql','oracle','ada','ocaml','kotlin','brainf','fortran','rust','clojure'];

if($text == '/start' or $text == '/help'){
	bot('sendMessage',[
        'chat_id'=>$chat_id,
        "parse_mode"=>"markdown",
        'text'=>"Execute code.

Usage: `/<language> <code> [/stdin <stdin>] `
        
Inline mode: `
@CodeTesterBot <language> <code> [/stdin <stdin>] `

Line breaks and indentation are supported.

I'll also try to execute files pm'ed to me.

See list of supported programming /languages.",
    ]);
}
elseif ($text == "/languages") {
    bot('sendMessage',[
        'chat_id'=>$chat_id,
        "parse_mode"=>"html",
        'text' => "/php, /asm, /bash, /csharp, /cppgcc, /cgcc, /cclang, /cojure, /commonlisp, /d, /erlang, /fsharp, /go, /python, /python3, /perl, /r, /ruby, /scala, /scheme, /sql_server, /swift, /tcl, <code>/vb.net</code>, /elixir, /erlang, /javascript, /lisp, /prolog, <code>/node.js</code>, /octave, /mysql, /postgresql, /oracle, /ada, /ocaml, /kotlin, /brainf, /fortran, /rust, /clojure",

    ]);
}
elseif(($lang = array_search(str_replace('/','',$text),$langtitle)) !== false){

	bot('sendMessage',[
        'chat_id'=>$chat_id,
        'reply_markup'=>json_encode(['force_reply'=>true]),
        'text'=>"Ok, give me some ".$langtitle[$lang]." code to execute",]);

}
elseif(isset($text) && isset($message->reply_to_message->text) && preg_match('/Ok, give me some (.*?) code to execute/', $message->reply_to_message->text, $type)){
    
    if($tillar[$type[1]] == 8)
    $code = '<?php ' . str_replace(['<?php','?>'],['',''],$text) . '?>';
    else
    $code = $text;
    
    $run = run($langs[$type[1]],$code);
    $Result = $run['Result'];
    $Errors = $run['Errors'];
    $Stats = $run['Stats'];
    bot('sendMessage',[
        'chat_id'=>$chat_id,
        'parse_mode'=>"Markdown",
        'text'=>"*Result:*\n``` $Result ```\n\n*Errors:*\n``` $Errors ```\n\n*Stats:*\n``` $Stats ```",
    ]);
}
else{
    bot('sendMessage',[
        'chat_id'=>$chat_id,
        'text'=>"Unknown language: $text",
    ]);
}

?>