<?php
require "data.php";
$update = json_decode(file_get_contents('php://input'));
if(isset($update->message) || isset($update->callback_query)):
$message = $update->message ;
$data=  $update->callback_query->data;
$id = $message->from->id ?? $update->callback_query->from->id;
$chat_id = $message->chat->id ?? $update->callback_query->message->chat->id;
$text = $message->text ;
$user = $message->from->username ?? $update->callback_query->from->username;
$name = $message->from->first_name ?? $update->callback_query->from->first_name;
$message_id = $message->message_id ?? $update->callback_query->message->message_id;
$type = $message->chat->type ?? $update->callback_query->message->chat->type;
$reply = $message->reply_to_message;
endif;
$link =  "https://".$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"];
echo file_get_contents("https://api.telegram.org/bot$token/setWebHook?url=$link");
$ex = explode ("#",$data);
if($id == $admin ){
	
	if($text == "/start" or $data == "back") {
		$info["admin"] = "";
		save();
		if($data=="back")
		$bot->deletemessage ([
			"chat_id"=>$chat_id,
			"message_id"=>$message_id
		]);
		$bot->sendmessage ([
			"chat_id"=>$chat_id,
			"text"=>trim($txt["القائمة الرئيسية"]),
			"reply_markup"=>json_encode([
				"inline_keyboard"=>[
					[["text"=>"اضافة دولة ➕","callback_data"=>"add"],
					["text"=>"حذف دولة 🗑️","callback_data"=>"del"]],
					[["text"=>"الدول المضافة 📊","callback_data"=>"all"]],
				]
			])
		]);
		
	} elseif($text == "/work") {
		$bot->sendmessage ([
			"chat_id"=>$chat_id,
			"text"=>trim($txt["تشغيل الصيد"])
		]);
		$info["status"]="work";
		save();
	} elseif ($text == "/stop") {
		$bot->sendmessage ([
			"chat_id"=>$chat_id,
			"text"=>trim($txt["ايقاف الصيد"])
		]);
		$info["status"]=null;
		save();
	} elseif ($data) {
		if($data == "all"){
			$all = join ("\n",$info["countries"]) ?? "لاتوجد دول مضافة";
			$bot->answercallbackquery([
				"callback_query_id" => $update->callback_query->id,
				"text"=>"$all",
				"show_alert"=>true,
			]);
		} elseif ($data == "add") {
			$bot->editmessagetext([
				"chat_id"=>$chat_id,
				"text"=>"قم بارسال رمز الدولة في موقع البوت",
				"message_id"=>$message_id,
				"reply_markup"=>json_encode([
					"inline_keyboard"=>[
						[["text"=>"رجوع🔙","callback_data"=>"back"]],
					]
				])
			]);
			$info["admin"] = "add";
			save();
		} elseif ($data == "del") {
			$bot->editmessagetext([
				"chat_id"=>$chat_id,
				"text"=>"قم بارسال كود الدولة",
				"message_id"=>$message_id,
				"reply_markup"=>json_encode([
					"inline_keyboard"=>[
						[["text"=>"رجوع🔙","callback_data"=>"back"]],
					]
				])
			]);
			$info["admin"] = "del";
			save();
		
		}
	} elseif ($text && $info["admin"] == "add") {
		$code = uniqid (1);
		$info["countries"][$code] = $text;
		$info["admin"] = "";
		save();
		$bot->sendmessage ([
			"chat_id"=>$chat_id,
			"text"=>"تمت الاضافة بنجاح\nكود الدولة\n$code\nيستخدم هذا الكود عند الرغبة بحذف الدولة"
		]);
	} elseif ($text && $info["admin"] == "del") {
		if($info["countries"][$text] == null){
			$bot->sendmessage ([
				"chat_id"=>$chat_id,
				"text"=>"لاتوجد دولة مضافة بهذا الكود"
			]);
			$info["admin"] = "";
			save();
		} else {
			unset($info["countries"][$text]);
			$info["admin"] = "";
			save();
			$bot->sendmessage ([
				"chat_id"=>$chat_id,
				"text"=>"تم الحذف بنجاح"
			]);
		}
	}
} 


if ($ex[0] == "getCode"  ) {
	$res = $api->getCode($ex[2],$ex[1]);
	if(empty ($res["Error"]) and $res["code"] != 0  ) {
		$code = $res["code"];
		$bot->editmessagetext([
			"chat_id"=>$chat_id,
			"text"=>trim(str_replace([
			"__code__","__number__"
			],
			[
			$code,$ex[2]
			],$txt["الكود"]
			)),
			"message_id"=>$message_id
		]);
	} else {
		$bot->answercallbackquery([
			"callback_query_id" => $update->callback_query->id,
			"text"=>"🚫 لم يصل الكود",
			"show_alert"=>true,
		]);
	}
} elseif ($ex[0] == "ban" ) {
	$res = $api->banNum($ex[2],$ex[1]);
	$bot->editmessagetext([
		"chat_id"=>$chat_id,
		"text"=>"تم حظر الرقم بنجاح",
		"message_id"=>$message_id
	]);
}






