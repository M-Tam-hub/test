<?php

require_once("require.php");

//リクエスト情報の取得
$p = GetRequestVal();


	//年月の取得
	if(isset($p["date_y"])){
		$date_y = $p["date_y"];
	}else{
		$date_y = date("Y");
	}
	if(isset($p["date_m"])){
		$date_m = $p["date_m"];
	}else{
		$date_m = date("m");
	}

	//該当年該当月（初日・末日）の取得
		$dateS = date("Y-m-d", mktime( 0,0,0,$date_m,01,$date_y ));
		$dateE = date("Y-m-d", mktime( 0,0,0,$date_m + 1,0,$date_y ) );
		$matujitu = substr($dateE , -2);
	//次月・前月の取得
		$befor_y = date("Y", mktime( 0,0,0,($date_m - 1),01,$date_y ));
		$befor_m = date("m", mktime( 0,0,0,($date_m - 1),01,$date_y ));
		$next_y = date("Y", mktime( 0,0,0,($date_m + 1),01,$date_y ));
		$next_m = date("m", mktime( 0,0,0,($date_m + 1),01,$date_y ));

	//初日の曜日判定
		$intFirst=date("w", mktime( 0,0,0,$date_m,01,$date_y ));
	//最終日の曜日を求める
		$intLast=date("w", mktime( 0,0,0,$date_m + 1,0,$date_y ));


//print_r($p);
if(isset($p["no"])){
	$no_temp = explode("_",$p["no"]);
	$tel_no = $no_temp[0];
	$cnt = $no_temp[1];
}

//エラー情報の初期化
$ErrMsg = "";

if(isset($p["logout"])){
	unset($_SESSION);
	session_destroy();
	header("Location: ./login.php");
	exit();
}



$check_ses = $ses->CheckSes();

if(!$check_ses){
	header("Location: ./login.php");
	exit();
}
//if($check_ses == "1"){
//	header("Location: ./select_job.php");
//	exit();
//}

	$myselect = new Myselect();			//リスト用
	$add_result = new Myselect();
	$List_member = new Myselect();


	//BL-----------------------------------------------------------------


	//部署一覧を取得
	$myselect->init();
	$res_syozoku = $myselect->select("*","mst_syozoku");
	if(is_array($res_syozoku)){
		foreach($res_syozoku as $key=>$val){
			//プルダウン情報を取得
			$myselect->init();
			$where_sub = "bumon_id='".$val["id"]."'";
			$arr_syozku_calendar = $myselect->select("*","dat_calender_setting",$where_sub);

			$arr_syozoku[$val["id"]] = array("syozoku_name" => $val["syozoku_name"],
											 "name_1" => $arr_syozku_calendar[0]["name_1"],
											 "col_1_1" => $arr_syozku_calendar[0]["col_1_1"],
											 "col_2_1" => $arr_syozku_calendar[0]["col_2_1"],
											 "name_2" => $arr_syozku_calendar[0]["name_2"],
											 "col_1_2" => $arr_syozku_calendar[0]["col_1_2"],
											 "col_2_2" => $arr_syozku_calendar[0]["col_2_2"],
											 "name_3" => $arr_syozku_calendar[0]["name_3"],
											 "col_1_3" => $arr_syozku_calendar[0]["col_1_3"],
											 "col_2_3" => $arr_syozku_calendar[0]["col_2_3"],
											 "name_4" => $arr_syozku_calendar[0]["name_4"],
											 "col_1_4" => $arr_syozku_calendar[0]["col_1_4"],
											 "col_2_4" => $arr_syozku_calendar[0]["col_2_4"],
											 "name_5" => $arr_syozku_calendar[0]["name_5"],
											 "col_1_5" => $arr_syozku_calendar[0]["col_1_5"],
											 "col_2_5" => $arr_syozku_calendar[0]["col_2_5"]
											);
			
			if($arr_syozku_calendar[0]["name_1"] != ""){		//１番目のアポ
				//アポイント情報を取得
				$myselect->init();
				$myselect->setorder("ap.apo_date, ap.apo_start_h, ap.apo_start_m");
				$col_str = "";
				if($arr_syozku_calendar[0]["col_1_1"] > 0){
					$col_str.= ",i.col_".$arr_syozku_calendar[0]["col_1_1"]." AS col1";
				}
				if($arr_syozku_calendar[0]["col_2_1"] > 0){
					$col_str.= ",i.col_".$arr_syozku_calendar[0]["col_2_1"]." AS col2";
				}
				$where_str = "ap.del_flg=0 AND ap.member_id = m.member_id AND ap.tel_no = b.tel_no AND ap.tel_no = i.tel_no AND ap.bumon_no = i.bumon_no AND ap.member_id = m.member_id AND ap.apo_date >='".$dateS."' AND ap.apo_date<='".$dateE."' and ap.bumon_no='".$val["id"]."' AND ap.calender_type = '1' ";
				$apo_list[$val["id"]]["1"] = $myselect->select("DISTINCT ap.*,b.col_1,m.member_name".$col_str,"dat_apoint ap, dat_base b, dat_info i, mst_member m",$where_str);
				if($apo_list == false){
					$ErrMsg = "アポイントデータ取得エラー";
				}
			}
			if($arr_syozku_calendar[0]["name_2"] != ""){		//2番目のアポ
				//アポイント情報を取得
				$myselect->init();
				$myselect->setorder("ap.apo_date, ap.apo_start_h, ap.apo_start_m");
				$col_str = "";
				if($arr_syozku_calendar[0]["col_1_2"] > 0){
					$col_str.= ",i.col_".$arr_syozku_calendar[0]["col_1_2"]." AS col1";
				}
				if($arr_syozku_calendar[0]["col_2_2"] > 0){
					$col_str.= ",i.col_".$arr_syozku_calendar[0]["col_2_2"]." AS col2";
				}
				$where_str = "ap.del_flg=0 AND ap.member_id = m.member_id AND ap.tel_no = b.tel_no AND ap.tel_no = i.tel_no AND ap.bumon_no = i.bumon_no AND ap.member_id = m.member_id AND ap.apo_date >='".$dateS."' AND ap.apo_date<='".$dateE."' and ap.bumon_no='".$val["id"]."' AND ap.calender_type = '2' ";
				$apo_list[$val["id"]]["2"] = $myselect->select("DISTINCT ap.*,b.col_1,m.member_name".$col_str,"dat_apoint ap, dat_base b, dat_info i, mst_member m",$where_str);
				if($apo_list == false){
					$ErrMsg = "アポイントデータ取得エラー";
				}
			}
			if($arr_syozku_calendar[0]["name_3"] != ""){		//3番目のアポ
				//アポイント情報を取得
				$myselect->init();
				$myselect->setorder("ap.apo_date, ap.apo_start_h, ap.apo_start_m");
				$col_str = "";
				if($arr_syozku_calendar[0]["col_1_3"] > 0){
					$col_str.= ",i.col_".$arr_syozku_calendar[0]["col_1_3"]." AS col1";
				}
				if($arr_syozku_calendar[0]["col_2_3"] > 0){
					$col_str.= ",i.col_".$arr_syozku_calendar[0]["col_2_3"]." AS col2";
				}
				$where_str = "ap.del_flg=0 AND ap.member_id = m.member_id AND ap.tel_no = b.tel_no AND ap.tel_no = i.tel_no AND ap.bumon_no = i.bumon_no AND ap.member_id = m.member_id AND ap.apo_date >='".$dateS."' AND ap.apo_date<='".$dateE."' and ap.bumon_no='".$val["id"]."' AND ap.calender_type = '3' ";
				$apo_list[$val["id"]]["3"] = $myselect->select("DISTINCT ap.*,b.col_1,m.member_name".$col_str,"dat_apoint ap, dat_base b, dat_info i, mst_member m",$where_str);
				if($apo_list == false){
					$ErrMsg = "アポイントデータ取得エラー";
				}
			}
			if($arr_syozku_calendar[0]["name_4"] != ""){		//4番目のアポ
				//アポイント情報を取得
				$myselect->init();
				$myselect->setorder("ap.apo_date, ap.apo_start_h, ap.apo_start_m");
				$col_str = "";
				if($arr_syozku_calendar[0]["col_1_4"] > 0){
					$col_str.= ",i.col_".$arr_syozku_calendar[0]["col_1_4"]." AS col1";
				}
				if($arr_syozku_calendar[0]["col_2_4"] > 0){
					$col_str.= ",i.col_".$arr_syozku_calendar[0]["col_2_4"]." AS col2";
				}
				$where_str = "ap.del_flg=0 AND ap.member_id = m.member_id AND ap.tel_no = b.tel_no AND ap.tel_no = i.tel_no AND ap.bumon_no = i.bumon_no AND ap.member_id = m.member_id AND ap.apo_date >='".$dateS."' AND ap.apo_date<='".$dateE."' and ap.bumon_no='".$val["id"]."' AND ap.calender_type = '4' ";
				$apo_list[$val["id"]]["4"] = $myselect->select("DISTINCT ap.*,b.col_1,m.member_name".$col_str,"dat_apoint ap, dat_base b, dat_info i, mst_member m",$where_str);
				if($apo_list == false){
					$ErrMsg = "アポイントデータ取得エラー";
				}
			}
			if($arr_syozku_calendar[0]["name_5"] != ""){		//5番目のアポ
				//アポイント情報を取得
				$myselect->init();
				$myselect->setorder("ap.apo_date, ap.apo_start_h, ap.apo_start_m");
				$col_str = "";
				if($arr_syozku_calendar[0]["col_1_5"] > 0){
					$col_str.= ",i.col_".$arr_syozku_calendar[0]["col_1_5"]." AS col1";
				}
				if($arr_syozku_calendar[0]["col_2_5"] > 0){
					$col_str.= ",i.col_".$arr_syozku_calendar[0]["col_2_5"]." AS col2";
				}
				$where_str = "ap.del_flg=0 AND ap.member_id = m.member_id AND ap.tel_no = b.tel_no AND ap.tel_no = i.tel_no AND ap.bumon_no = i.bumon_no AND ap.member_id = m.member_id AND ap.apo_date >='".$dateS."' AND ap.apo_date<='".$dateE."' and ap.bumon_no='".$val["id"]."' AND ap.calender_type = '5' ";
				$apo_list[$val["id"]]["5"] = $myselect->select("DISTINCT ap.*,b.col_1,m.member_name".$col_str,"dat_apoint ap, dat_base b, dat_info i, mst_member m",$where_str);
				if($apo_list == false){
					$ErrMsg = "アポイントデータ取得エラー";
				}
			}
		}
	}


/*
	//アポイント情報を取得
	$myselect->init();
	$where_str = "ap.tel_no = b.tel_no and ap.member_id=m.member_id AND ap.tel_no ='".$tel_no."'";
	$apo_list = $myselect->select("ap.*,b.col_1,m.member_name","dat_apoint ap,dat_base b,mst_member m",$where_str);
	if($apo_list == false){
		$ErrMsg = "アポイントデータ取得エラー";
	}
*/

include("./tpl/apo_calendar.tpl");

?>