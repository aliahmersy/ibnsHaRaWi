<?php
require "data.php";
if($info["status"] == "work"){
	if(! empty ($info["countries"])){
		for($i=0;$i<100;$i++) {
			foreach ($info["countries"] as $country) {
				$res = $api->getNumber($country);
				if(empty ($res["Error"] )) {
					$id = $res["id"];
					$num = $res["num"];
					if(empty ($id) || empty ($num)) continue;
					$bot->sendmessage ([
						"chat_id"=>$ch,
						"text"=>str_replace([
							"__number__"
						],[
							$num
						],trim($txt["رسالة الصيد"])),
						"parse_mode"=>"markdown",
						"reply_markup"=>json_encode([
							"inline_keyboard"=>[
								[["text"=>trim($txt["طلب الكود"]),"callback_data"=>"getCode#$id#$num"]],
								[["text"=>trim($txt["حظر الرقم"]),"callback_data"=>"ban#$id#$num"]]
							]
						])
					]);
					usleep(100000);
				} else { continue; }
			}
		}
	} else { exit; }
} else { exit; }








