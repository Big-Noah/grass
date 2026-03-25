
$(function () {
	/*color click*/
	$("#lens_change_color").click(function(){
		$("#lens_left_close").click();
	})
	/*help hover*/
    $('[data-toggle="popover"]').popover({
		'trigger':'hover ',
		'html':true,
	})
    /**/
	$("body").on("click", ".lens_key", function (e) {	
		var t_step = Number($(this).attr('step'));
		if(t_step==1){
			usage = $(this).attr('val');
			readers = 0;
			$("#step_1_cn").html(usage_name[usage]+"&nbsp;&nbsp;<span class='un_line'>&nbsp;EDIT&nbsp;</span>");
			clear_index();
		}

		if(t_step==3){
			lenstype = $(this).attr('val');
			if(lenstype==1 || lenstype==2 || lenstype==8){
				lenstype_color = 0; 
				$(".mid-cspan").removeClass("mid-choose-color");
			}else{
				if(lenstype_color==0){
					$.MsgBox.Alert("Tips", "Please select a color.");
					return false;
				}
			}
			if(lenstype_color != 0){
				var lenstype_color_name = '&nbsp;&nbsp;Color:'+lenscolor_name[lenstype_color][1];
			}else{
				var lenstype_color_name = '';
			}
			if(lenstype_color==52 || lenstype==7){
				removeBluelight(2);
			}else{
				if(usage!=3){
					unlockBluelight();
				}
			}

			$("#step_3_cn").html(lenstype_name[lenstype]+lenstype_color_name+"&nbsp;&nbsp;<span class='un_line'>&nbsp;EDIT&nbsp;</span>");
		}

		if(t_step==4){
			lensindex = $(this).attr('val');
			$("#step_4_cn").html(lensindex_name[lensindex]+"&nbsp;&nbsp;<span class='un_line'>&nbsp;EDIT&nbsp;</span>");
		}

		if(t_step==5){
			coating = $(this).attr('val');
			$("#step_5_cn").html(coating_name[coating]+"&nbsp;&nbsp;<span class='un_line'>&nbsp;EDIT&nbsp;</span>");
		}

		count_price();
		hide_lens_entry();
		$("#step"+t_step+"_div_box").find(".lens_k_choose").removeClass('lens_k_choose');
		$("#step"+t_step+"_li_"+$(this).attr('val')).addClass('lens_k_choose');
		if(goodsdata.type==3){
			if(t_step!=4){
				show_one_collapse((t_step+1));
			}else{
				//$(".collapse").collapse('hide');
				confimProgressive();
			}
		}else{
			if(t_step!=5){
				show_one_collapse((t_step+1));
			}else{
				//$(".collapse").collapse('hide');
				confimProgressive();
			}
		}
		//console.log(readers);
	})
})


/*STEP_1 START*/
function check_step1_val(){
	if(usage == 30){ return true;}
	if(usage==0){
		$.MsgBox.Alert("Tips", 'Please select a Usage!');
		return false;
	}
	return true;
}
function clear_index(){
	lenstype = 0;
	lensindex = 0;
	coating = 0;
	$("#step3_div_box,#step4_div_box,#step5_div_box").removeClass('show');
	$("#step_3_card,#step_4_card,#step_5_card").addClass('disabled_card');
	$("#step_3_cn,#step_4_cn,#step_5_cn").html("");
	$("#step_3_card .lens_k_choose,#step_4_card .lens_k_choose,#step_5_card .lens_k_choose").removeClass('lens_k_choose');
}
/*STEP_1 END*/

/*STEP_2 START*/
	function check_step2_val(){
		if(usage==2 && readers==1 && power!=0){ return true;}
		if(usage==20 || usage==30){ return true;}

		if(rximg=='' && $("#od_sph").val()==0 && $("#os_sph").val()==0 && $("#od_cyl").val()==0 && $("#os_cyl").val()==0 && $("#od_add").val()==0 && $("#os_add").val()==0 ){
			$.MsgBox.Alert("Tips", "Your prescription has no value, please confirm your prescription. Do you want to order lenses without a prescription? Please select non-prescription type instead.");
			return false;
		}
		if(($("#od_cyl").val()!=0 && Number($("#od_axis").val())<=0) || ($("#os_cyl").val()!=0 && Number($("#os_axis").val())<=0) ){
			$.MsgBox.Alert("Tips", "You chose CYL value, please add the Effective Axis value.");
			return false;
		}
		if(pdkey==2 && (Number($("#od_pd").val())<=0 || Number($("#os_pd").val())<=0)){
			$.MsgBox.Alert("Tips", "Please select your pupillary distance (PD). Pupillary distance or PD is the distance from the center of the pupil in one eye to the center of the pupil in the other eye.");
			return false;
		}

		/*
		if((usage==2) && ($("#od_add").val()==0 && $("#os_add").val()==0)){
			$.MsgBox.Alert("Tips", "Please make sure to enter the Addition (ADD) value if it is listed in your prescription, otherwise proceed with the order.");
			return false;
		}
		*/		
		if(rximg=='' && (usage==3 || usage==4 || usage==5) && ($("#od_add").val()==0 || $("#os_add").val()==0)){
			if(usage==3){
				$.MsgBox.Alert("Tips", "You selected Bifocal Lenses (with line) as primary use of your glasses, please input your ADD for the reading portion of your glasses.");
			}
			if(usage==4 || usage==5){
				$.MsgBox.Alert("Tips", "You selected Progressive as primary use of your glasses, please input your ADD for the reading portion of your glasses.");
			}			
			return false;
		}

		if(usage==3){
			var od_sph_rv = Math.abs(Number($("#od_sph").val()));
			var os_sph_rv = Math.abs(Number($("#os_sph").val()));
			var od_sph_add = Math.abs(Number($("#od_sph").val()) + Number($("#od_add").val()));
			var os_sph_add = Math.abs(Number($("#os_sph").val()) + Number($("#os_add").val()));
			if(od_sph_rv>6.00 || os_sph_rv>6.00 || od_sph_add>6.00 || os_sph_add>6.00){
				$.MsgBox.Alert("Tips", "Unfortunately, the lens you selected is not available for your prescription. We recommend you look into the versatility of progressive lenses.");
				return false;
			}
		}
		

		if(prism==1 && ($("#od_prismnum_v").val()==0 && $("#os_prismnum_v").val()==0 && $("#od_prismnum_h").val()==0 && $("#os_prismnum_h").val()==0)){
			$.MsgBox.Alert("Tips", "Please choose the prism value.");
			return false;			
		}
		if(prism==1 && ($("#od_prismdir_v").val()==0 && $("#os_prismdir_v").val()==0 && $("#od_prismdir_h").val()==0 && $("#os_prismdir_h").val()==0)){
			$.MsgBox.Alert("Tips", "Please choose the base direction of your prism.");
			return false;			
		}
		if($("#lens_comment").val().length > 200){
			$.MsgBox.Alert("Tips", "Comment up to 200 characters.");
			return false;			
		}
		/*
		if(Number($("#birth_year").val())==0){
			$.MsgBox.Alert("Tips", "Birth Year is required. Please select the birth year tied to this prescription.");
			return false;			
		}
		if($("#rx_name").val()==""){
			$.MsgBox.Alert("Tips", "Please give a name for your prescription.");
			return false;			
		}
		*/
		return true;
	}
	/*sph*/
	$("body").on("change", "#od_sph,#os_sph", function (e) {
		if( ($("#od_sph").val()>0 && $("#os_sph").val()<0) || ($("#od_sph").val()<0 && $("#os_sph").val()>0)){
			$.MsgBox.Alert("ARE YOU SURE ?", "That's unusual! For most people, both eyes have either negative ( - ) or positive ( + ) SPH values. Are you sure your prescription shows both?","SURE"); return false;
		}
	})
	/*cyl变化*/
	$("body").on("change", "#od_cyl,#os_cyl", function (e) {	
		change_cyl();
		if(($("#od_cyl").val()>0 && $("#os_cyl").val()<0) || ($("#od_cyl").val()<0 && $("#os_cyl").val()>0)){
			$.MsgBox.Alert("Tips", "That's unusual! For most people, Cylinder is either Negative (-) or positive (+). Are you sure your prescription shows both?"); return false;
		}
	})
	function change_cyl(){
		if($("#od_cyl").val()=="0.00" && $("#os_cyl").val()=="0.00"){
			$(".sr-axis").addClass('sr-disabled');
			//$("#od_axis,#os_axis").attr("disabled","disabled");
		}else{
			$(".sr-axis").removeClass('sr-disabled');
			//$("#od_axis,#os_axis").removeAttr("disabled");
		}
		
		if($("#od_cyl").val()!=0.00){
			$("#od_axis").removeAttr("disabled");
		}else{
			$("#od_axis").attr("disabled","disabled");
			$("#od_axis").val("");
		}

		if($("#os_cyl").val()!=0.00){
			$("#os_axis").removeAttr("disabled");
		}else{
			$("#os_axis").attr("disabled","disabled");
			$("#os_axis").val("");
		}

	}
	/*add变化*/
	$("body").on("change", "#od_add,#os_add", function (e) {	
		if( ($("#od_add").val()!=0 && $("#os_add").val()!=0) && ($("#od_add").val() != $("#os_add").val())){
			$.MsgBox.Alert("ARE YOU SURE ?", "That's unusual! For most people, Addition (ADD) values are the same for both eyes.","SURE"); return false;
		}
		if($("#od_add").val()!=0 || $("#os_add").val()!=0){
			//$(".sr-near-pd-checkbox").slideDown(200);
		}else{
			//$(".sr-near-pd-checkbox").slideUp(200);
		}
	})
	/*pd变化*/
	$("body").on("change", "#pd", function (e) {	
		if($("#pd").val()<46){
			$.MsgBox.Alert("A PD ERROR OCCURRED", 'Your PD is less than 46mm. This is very unusual. We suggest you confirm your PD.'); return false;
		}
	})
	$("body").on("change", "#od_pd,#os_pd", function (e) {	
		
		if( ($("#od_pd").val()!=0 && $("#os_pd").val()!=0) && (Number($("#od_pd").val()) + Number($("#od_pd").val()))<46 ){
			$.MsgBox.Alert("A PD ERROR OCCURRED", 'Your PD is less than 46mm. This is very unusual. We suggest you confirm your PD.'); return false;
		}
	})
	/*
		最小pd的计算公式
		var checknum1 = parseFloat(zxj)+parseFloat(zdzj)-72; //近视，中心距，最大直径
	    var checknum2 = parseFloat(zxj)+parseFloat(zdzj)-67; //老花，中心距，最大直径
	*/

	/*near pd*/
	$("body").on("click", ".sr-near-pd-checkbox", function (e) {	
		if(nearpd==0){
			nearpd=1;
			$(".sr-near-pd-checkbox").addClass("nearpd-check");
			$(".near-pd-ul").slideDown(200);
		}else{
			nearpd=0;
			$(".sr-near-pd-checkbox").removeClass("nearpd-check");
			$(".near-pd-ul").slideUp(200);
		}
	})
	/*two pd*/
	$("body").on("click", ".sr-pd-checkbox", function (e) {	
		if(pdkey==1){
			pdkey=2;
			$(".pd-add-one").hide();
			$(".pd-add-two").show();
			$(".sr-pd-checkbox").addClass("sr-twopd");
		}else{
			pdkey=1;
			$(".pd-add-one").show();
			$(".pd-add-two").hide();
			$(".sr-pd-checkbox").removeClass("sr-twopd");
		}
	})
	/*prime*/
	$("body").on("click", "#prime-checkbox", function (e) {	
		if(prism==0){
			prism=1;
			$("#prime-checkbox").addClass("prime-check");
			$("#prime-div").slideDown(200);
		}else{
			prism=0;
			$("#prime-checkbox").removeClass("prime-check");
			$("#prime-div").slideUp(200);
		}
		count_price();
	})

	function step2_check_modal(){
		$("#lm_usage").html(usage_name[usage]);
		var lm_npd = (nearpd==0)?'NO':$("#npd").val();
		$("#lm_nearpd").html(lm_npd);
		//$("#lm_birth_year").html($("#birth_year").val());

		$("#lm_od_sph").html($("#od_sph").val());
		$("#lm_os_sph").html($("#os_sph").val());
		$("#lm_od_cyl").html($("#od_cyl").val());
		$("#lm_os_cyl").html($("#os_cyl").val());
		$("#lm_od_axis").html($("#od_axis").val());
		$("#lm_os_axis").html($("#os_axis").val());
		$("#lm_od_add").html($("#od_add").val());
		$("#lm_os_add").html($("#os_add").val());
		if(pdkey==1){
			$("#lm_pd").html($("#pd").val());
			$("#lens_checkModal .one_pd").show();
			$("#lens_checkModal .two_pd").hide();
		}else{
			$("#lm_od_pd").html($("#od_pd").val());
			$("#lm_os_pd").html($("#os_pd").val());
			$("#lens_checkModal .one_pd").hide();
			$("#lens_checkModal .two_pd").show();
		}
		$("#lens_checkModal").modal();
	}
	//submit prescription
	$("#lens-setp2-sure").click(function(){
		if(check_step2_val()){
			step2_check_modal();
		}
	})	

	$("#lens_checkModal .modifying_pre").click(function(){
		$("#lens_checkModal").modal('hide');
	})	

	$("#lens_checkModal .confirm_pre").click(function(){
		$("#lens_checkModal").modal('hide');
		save_prescription_sure();
	})

	function save_prescription_sure(){
		$.MsgBox.Loading();
	  	setTimeout(function(){
		  $.ajax({
		    url:'/lens/save_prescription',
		    type: "POST",
		    data: ({
		    	'login_key':login_key,
		    	'pdkey':pdkey,
		    	'nearpd':nearpd,
		    	'prism':prism,
		    	'rxkey':rxkey,
		    	'od_sph':$("#od_sph").val(),
		    	'os_sph':$("#os_sph").val(),
		    	'od_cyl':$("#od_cyl").val(),
		    	'os_cyl':$("#os_cyl").val(),
		    	'od_axis':$("#od_axis").val(),
		    	'os_axis':$("#os_axis").val(),
		    	'od_add':$("#od_add").val(),
		    	'os_add':$("#os_add").val(),
		    	'pd':$("#pd").val(),
		    	'od_pd':$("#od_pd").val(),
		    	'os_pd':$("#os_pd").val(),
		    	'npd':$("#npd").val(),
		    	'birth_year':$("#birth_year").val(),
		    	'od_prismnum_v':$("#od_prismnum_v").val(),
		    	'os_prismnum_v':$("#os_prismnum_v").val(),
		    	'od_prismdir_v':$("#od_prismdir_v").val(),
		    	'os_prismdir_v':$("#os_prismdir_v").val(),
		    	'od_prismnum_h':$("#od_prismnum_h").val(),
		    	'os_prismnum_h':$("#os_prismnum_h").val(),
		    	'od_prismdir_h':$("#od_prismdir_h").val(),
		    	'os_prismdir_h':$("#os_prismdir_h").val(),
		    	'lens_comment':$("#lens_comment").val(),
		    	'rx_name':$("#rx_name").val(),
		    	'rximg':rximg,
			}),
		    dataType: "json",
		    async: false, 
		    beforeSend:function(){},
		    success:function(res){
		    	$.MsgBox.Close();
		    	if(res.code==1){
		    		var list = res.list;
		    		var html = '<option value="0">Select Prescription</option>';
		    		$.each(list,function(k,lv){
						html = html+'<option value="'+lv['id']+'">'+lv['name']+'</option>';
					})
		    		$("#prescription").html(html);
		    		$("#prescription").val(res.id);

		    		$("#step_2_cn").html($("#rx_name").val()+"&nbsp;&nbsp;<span class='un_line'>&nbsp;EDIT&nbsp;</span>");
		    		count_price();
		    		show_one_collapse(3);
		    	}else{
					$.MsgBox.Alert("ERROR", res.info);
		    	}
		    },error:function(e){
		    	$.MsgBox.Close();
				$.MsgBox.Alert("ERROR", "ERROR, Please try again.");
		    }
		  });
	  	},200)
	}

	/*choose prescription*/
	$("body").on("change", "#prescription", function (e) {	
		if($("#prescription").val()!=0){
			prescription = $("#prescription").val();
			if(prescription_list[prescription]){
				var pre_r = prescription_list[prescription];
				change_prescription(pre_r);
			}
		}
	})

	function change_prescription(pre_r,editlens){
		
		if(pre_r){
			//console.log(typeof(pre_r));
			if(pre_r['pdkey']==1){
				pdkey = 1;
				$(".pd-add-one").show();
				$(".pd-add-two").hide();
				$(".sr-pd-checkbox").removeClass("sr-twopd");
			}else{
				pdkey = 2;
				$(".pd-add-one").hide();
				$(".pd-add-two").show();
				$(".sr-pd-checkbox").addClass("sr-twopd");
			}
			//nearpd
			if(pre_r['nearpd']==1){
				nearpd=1;
				$(".sr-near-pd-checkbox").addClass("nearpd-check");
				$(".near-pd-ul").slideDown(200);
			}else{
				nearpd=0;
				$(".sr-near-pd-checkbox").removeClass("nearpd-check");
				$(".near-pd-ul").slideUp(200);
			}
			$("#od_sph").val(pre_r['od_sph']);
			$("#os_sph").val(pre_r['os_sph']);
			$("#od_cyl").val(pre_r['od_cyl']);
			$("#os_cyl").val(pre_r['os_cyl']);
			$("#od_axis").val(pre_r['od_axis']);
			$("#os_axis").val(pre_r['os_axis']);
			
			$("#od_add").val(pre_r['od_add']);
			$("#os_add").val(pre_r['os_add']);

			$("#pd").val(pre_r['pd']);
			$("#od_pd").val(pre_r['od_pd']);
			$("#os_pd").val(pre_r['os_pd']);
			$("#npd").val(pre_r['npd']);
			$("#birth_year").val(pre_r['birth_year']);
			$("#od_prismnum_v").val(pre_r['od_prismnum_v']);
			$("#os_prismnum_v").val(pre_r['os_prismnum_v']);
			$("#od_prismdir_v").val(pre_r['od_prismdir_v']);
			$("#os_prismdir_v").val(pre_r['os_prismdir_v']);
			$("#od_prismnum_h").val(pre_r['od_prismnum_h']);
			$("#os_prismnum_h").val(pre_r['os_prismnum_h']);
			$("#od_prismdir_h").val(pre_r['od_prismdir_h']);
			$("#os_prismdir_h").val(pre_r['os_prismdir_h']);
			if(pre_r['rxkey']==1 && typeof(pre_r['name'])!= 'undefined'){
				$("#rx_name").val(pre_r['name']);
			}
			if(pre_r['prism']==1){
				prism=1;
				$("#prime-checkbox").addClass("prime-check");
				$("#prime-div,#more-option-box").slideDown(200);
			}else{
				prism=0;
				$("#prime-checkbox").removeClass("prime-check");
				$("#prime-div").slideUp(200);
			}
			if(pre_r['rximg']!="" && pre_r['rximg']!="null" && pre_r['rximg']!=undefined){
				setRximg(pre_r['rximg']);
			}else{
				clearRximg();
			}
			change_cyl();
			changesetp(2);
		}
	}
	$("#add_prescription_new").click(function(){
		prescription = 0;
		pdkey = 1; nearpd = 0;prism = 0;
		$("#prescription").val(0);
		$("#rx_name").val('');
		$("#od_sph,#od_cyl,#os_sph,#os_cyl").val('0.00');
		$("#od_axis,#os_axis").val('');
		$(".pd-add-one").show();
		$(".pd-add-two,.near-pd-ul,#prime-div").hide();
		$("#prime-checkbox").removeClass("prime-check");
		$(".sr-pd-checkbox").removeClass("sr-twopd");
		$(".sr-near-pd-checkbox").removeClass("nearpd-check");
		$("#pd,#npd").val('63');
		$("#od_add,#os_add,#od_pd,#os_pd").val('0');
		$("#od_prismnum_v,#os_prismnum_v,#od_prismdir_v,#os_prismdir_v,#od_prismnum_h,#os_prismnum_h,#od_prismdir_h,#os_prismdir_h").val('0');
		clearRximg();
		change_cyl();
	})

	function clear_stpe_2(){
		$("#step_2_card").collapse('hide');
		$("#step_2_card").addClass('disabled_card');
		$("#step_2_cn").html("");
	}

	$("body").on("click", ".power_v", function (e) {	
		$('.power_v').removeClass('power_v_choose');
		$(this).addClass('power_v_choose');
		power = $(this).attr("power");
	})
	
	$("body").on("click", "#power-sure", function (e) {	
		$("#step_2_cn").html("Readers (+"+power+")&nbsp;&nbsp;<span class='un_line'>&nbsp;EDIT&nbsp;</span>");
		count_price();
		show_one_collapse(3);
	})

	$("body").on("click", ".strength_btn", function (e) {	
		$("#strength_info").slideToggle();
	})

	
/*STEP_2 END*/


/*STEP_3 START*/
	function check_step3_val(){
		if(usage == 30){ return true;}
		if(lenstype == 0){
			$.MsgBox.Alert("Tips", "Please choose the lens type.");
			return false;
		}
		if(($.inArray(lenstype,['3','4','5','6','7'])>=0) && lenstype_color == 0){		
			$.MsgBox.Alert("Tips", "Please select a color.");
			return false;
		}
		return true;
	}
	/*打开二级选项*/
	$("body").on("click", ".lens_entry", function (e) {	
		hide_lens_entry();
		$(this).addClass('lens_entry_choose');
		$("#"+$(this).attr("sec")).find('.borderd7').addClass('lens_entry_choose');
		$("#"+$(this).attr("sec")).slideDown(200);
		lenstype_color = 0;
		$(".mid-cspan").removeClass("mid-choose-color");
		$(".dark-s").hide();
	})
	/*隐藏二级选项*/
	function hide_lens_entry(){
		$(".lens_entry_info").hide();
		$(".lens_entry_choose").removeClass('lens_entry_choose');
	}
	$("body").on("click", ".color_btn", function (e) {	
		$(this).parent().find(".mid-cspan").removeClass("mid-choose-color");
		$(this).addClass("mid-choose-color");
		lenstype_color = $(this).attr('val');
		if(lenstype_color != 0){
			ltv = $(this).attr('ltv');
			$(".cs_cname").html("");
			$("#cs_cname_"+ltv).html("&nbsp;&nbsp;("+lenscolor_name[lenstype_color][1]+")");
		}
		if($.inArray(lenstype_color,['2','3','4','5','6','7','8'])>=0){
			$(".dark-s").show();
			$(".dark-s").html('Darkness 80%');
		}else if($.inArray(lenstype_color,['41','42','43','44','45','46'])>=0){
			$(".dark-s").show();
			$(".dark-s").html('Gradient 60%');
		}else if($.inArray(lenstype_color,['31','32','33','34','35','36','37','38'])>=0){
			$(".dark-s").show();
			$(".dark-s").html('Gradient 80%-10%');
		}else{
			$(".dark-s").hide();
		}
		//Gradient 80%-10%

	})
	function change_lenstype(){
		$("#step3_div_box .step3_panel").addClass('disable_step_panel');
		$("#lensc_3_53,#lensc_3_54,#lensc_3_55").show();
		$("#lensc_6_4,#lensc_6_5,#lensc_6_6,#lensc_6_7,#lensc_6_8").show();
/*
		if(goodsdata.type==3){
			$("#step3_li_1,#step3_li_2,#step3_li_3").hide();
			$("#step3_li_4,#step3_li_5,#step3_li_6").removeClass('disable_step_panel');
		}else{			
		}
*/	
		if(usage == 1 || usage == 2 || usage == 20){
			$("#step3_li_1,#step3_li_2,#step3_li_3,#step3_li_4,#step3_li_5,#step3_li_6,#step3_li_7,#step3_li_8").removeClass('disable_step_panel');
		}else if(usage == 3){
			$("#step3_li_1,#step3_li_3,#step3_li_4").removeClass('disable_step_panel');
			$("#lensc_3_53,#lensc_3_54,#lensc_3_55").hide();//
		}else if(usage == 4){
			$("#step3_li_1,#step3_li_2,#step3_li_3,#step3_li_4,#step3_li_6,#step3_li_7").removeClass('disable_step_panel');
			$("#lensc_6_4,#lensc_6_5,#lensc_6_6,#lensc_6_7,#lensc_6_8").hide();
		}else if(usage == 5){
			$("#step3_li_1,#step3_li_2,#step3_li_3,#step3_li_4,#step3_li_6,#step3_li_7,#step3_li_8").removeClass('disable_step_panel');
			$("#lensc_6_4,#lensc_6_5,#lensc_6_6,#lensc_6_7,#lensc_6_8").hide();
		}
		//console.log(usage);
		change_lenstype_price();
	}
	function change_lenstype_price(){
		//step_3_i_price
		for (var ti = 1; ti <= Object.keys(lenstype_name).length; ti++) {
			$("#step_3_"+ti+"_price").html(s_price(lenstype_price[usage][ti],1));
		}
	}
/*STEP_3 END*/

/*STEP_4 START*/
	function check_step4_val(){
		if(usage == 30){ return true;}
		if(lensindex == 0){
			$.MsgBox.Alert("Tips", "Please choose the LensIndex.");
			return false;
		}
		return true;
	}
	function change_lensindex(){
		$("#step4_div_box .step4_panel").addClass('disable_step_panel');
		
		if(goodsdata.type==3){
			$("#step4_li_6").hide();
			$("#step4_li_2,#step4_li_3,#step4_li_4,#step4_li_5").removeClass('disable_step_panel');
		}else{
			if(usage == 1 || usage == 2 || usage == 20){
				$("#step4_li_2,#step4_li_3,#step4_li_4,#step4_li_5").removeClass('disable_step_panel');
			}else if(usage == 3){
				$("#step4_li_2,#step4_li_3").removeClass('disable_step_panel');
			}else if(usage == 4){
				$("#step4_li_2,#step4_li_3,#step4_li_4,#step4_li_5").removeClass('disable_step_panel');
			}else if(usage == 5){
				$("#step4_li_2,#step4_li_3,#step4_li_4,#step4_li_5").removeClass('disable_step_panel');
			}
		}
		if(lenstype==3 && (lenstype_color==53 || lenstype_color==54 || lenstype_color==55)){
			$("#step4_li_5").addClass('disable_step_panel');
		}
		if(lenstype==8){
			$("#step4_li_2,#step4_li_5").addClass('disable_step_panel');
		}
		if(usage!=20 && usage!=30 && readers!=1 && ( Math.abs(Number($("#od_sph").val()))>3.75 || Math.abs(Number($("#os_sph").val()))>3.75 || Math.abs(Number($("#od_cyl").val()))>3.75 || Math.abs(Number($("#os_cyl").val()))>3.75 )){
			$("#step4_li_2").addClass('disable_step_panel');//处方大于 3.75
		}

		if(usage!=20 && usage!=30 && readers!=1 && ( Math.abs(Number($("#od_sph").val()))>7.75 || Math.abs(Number($("#os_sph").val()))>7.75 || Math.abs(Number($("#od_cyl").val()))>7.75 || Math.abs(Number($("#os_cyl").val()))>7.75 )){
			$("#step4_li_3").addClass('disable_step_panel');//处方大于 7.75
		}


		if(usage!=20 && usage!=30 && readers!=1 && ( Math.abs(Number($("#od_sph").val()))>10 || Math.abs(Number($("#os_sph").val()))>10 || Math.abs(Number($("#od_cyl").val()))>10 || Math.abs(Number($("#os_cyl").val()))>10 )){
			$("#step4_li_4").addClass('disable_step_panel');//处方大于 10
		}


		if(usage==2 && readers==1 && (power>2.75)){
			$("#step4_li_2").addClass('disable_step_panel');//power大于 2.75
		}

		change_recommended_lensindex();
		change_lensindex_price();
	}
	function change_lensindex_price(){
		for (var ti = 1; ti <= Object.keys(lensindex_name).length; ti++) {
			$("#step_4_"+ti+"_price").html(s_price(lensindex_price[usage][ti]));
		}
	}


	function change_recommended_lensindex(){
		$(".recommended-icon").hide();
		var od_sph_rv = Math.abs(Number($("#od_sph").val()));
		var os_sph_rv = Math.abs(Number($("#os_sph").val()));
		var od_cyl_rv = Math.abs(Number($("#od_cyl").val()));
		var os_cyl_rv = Math.abs(Number($("#os_cyl").val()));
		

		var od_sph_rv_add = Math.abs( Number($("#od_sph").val()) + Number($("#od_add").val()) );
		var os_sph_rv_add = Math.abs( Number($("#os_sph").val()) + Number($("#os_add").val()) );

		//Single Vision 
		if(usage == 1 || (usage == 2 && readers!=1)){
			if( od_sph_rv>8.00 || os_sph_rv>8.00 || od_cyl_rv>8.00 || os_cyl_rv>8.00 ){
				$(".recommended-icon-5").css('display','inline-block');//1.74
			}else if(od_sph_rv>6.00 || os_sph_rv>6.00 || od_cyl_rv>6.00 || os_cyl_rv>6.00){
				$(".recommended-icon-4").css('display','inline-block');//1.67
			}else if(od_sph_rv>2.25 || os_sph_rv>2.25 || od_cyl_rv>2.25 || os_cyl_rv>2.25){
				$(".recommended-icon-3").css('display','inline-block');//1.61
			}
		}

		//Reader Power
		if(usage == 2 && readers==1){
			if(power>6.25){
				$(".recommended-icon-5").css('display','inline-block');
			}else if(power>3.50){
				$(".recommended-icon-4").css('display','inline-block');
			}else if(power>2.00){
				$(".recommended-icon-3").css('display','inline-block');
			}
		}

		//Bifocal & Progressive
		if(usage == 3 || usage==4){
			if(od_sph_rv>5.75 || os_sph_rv>5.75 || od_cyl_rv>5.75 || os_cyl_rv>5.75 || od_sph_rv_add>5.75 || os_sph_rv_add>5.75){
				$(".recommended-icon-4").css('display','inline-block');
			}else if(od_sph_rv>2.25 || os_sph_rv>2.25 || od_cyl_rv>2.25 || os_cyl_rv>2.25 || od_sph_rv_add>2.25 || os_sph_rv_add>2.25){
				$(".recommended-icon-3").css('display','inline-block');
			}
		}

		//Premium Progressive
		if(usage==5){
			if(od_sph_rv>7.25 || os_sph_rv>7.25 || od_cyl_rv>7.25 || os_cyl_rv>7.25 || od_sph_rv_add>7.25 || os_sph_rv_add>7.25){
				$(".recommended-icon-5").css('display','inline-block');
			}else if(od_sph_rv>5.25 || os_sph_rv>5.25 || od_cyl_rv>5.25 || os_cyl_rv>5.25 || od_sph_rv_add>5.25 || os_sph_rv_add>5.25){
				$(".recommended-icon-4").css('display','inline-block');
			}else if(od_sph_rv>2.25 || os_sph_rv>2.25 || od_cyl_rv>2.25 || os_cyl_rv>2.25 || od_sph_rv_add>2.25 || os_sph_rv_add>2.25){
				$(".recommended-icon-3").css('display','inline-block');
			}
		}

		if(lenstype==3 && (lenstype_color==53 || lenstype_color==54 || lenstype_color==55)){
			$(".recommended-icon-5").hide();
		}

	}

/*STEP_4 END*/

/*STEP_5 START*/
	function check_step5_val(){
		if(usage == 30){ return true;}
		if(coating == 0 && goodsdata.type!=3){
			$.MsgBox.Alert("Tips", "Please choose the coating.");
			return false;
		}
		return true;
	}
/*STEP_6 END*/

/*count_price */
function count_price(){
	lens_price = 0; 
	usage_p = 0; prism_p = 0; lenstype_p = 0; lensindex_p = 0; coating_p = 0;

	if(usage!=0){
		//lens_price = Number(lens_price).add(usage_price[usage][1]);
		//usage_p = "$"+usage_price[usage][1];
		if(usage==2 && readers==1){
			$("#deta_usage").html("Readers (+"+power+")");
		}else{
			$("#deta_usage").html(usage_name[usage]);
		}
		//$("#deta_usage_p").html(usage_p);
		$("#data_attr_1").show();
	}else{
		$("#data_attr_1").hide();
	}

	if(prex_price[prism]){
		lens_price = Number(lens_price).add(prex_price[prism][1]);
		prism_p = "$"+prex_price[prism][1];
		$("#deta_lenst_prism").html('YES');
		$("#deta_lenst_prism_p").html(prism_p);	
		$("#data_attr_2").show();
	}else{
		$("#deta_lenst_prism").html('NONE');
		$("#data_attr_2").hide();
	}

	if(lenstype!=0){
		lens_price = Number(lens_price).add(lenstype_price[usage][lenstype][4]);
		lenstype_p = "$"+lenstype_price[usage][lenstype][4];
		$("#deta_lenst_type").html(lenstype_name[lenstype]);
		$("#deta_lenst_type_p").html(lenstype_p);
		$("#data_attr_3").show();
	}else{
		$("#data_attr_3").hide();
	}

	if(lensindex!=0){
		lens_price = Number(lens_price).add(lensindex_price[usage][lensindex][1]);
		lensindex_p = "$"+lensindex_price[usage][lensindex][1];
		$("#deta_lenst_index").html(lensindex_name[lensindex]);
		$("#deta_lenst_index_p").html(lensindex_p);
		$("#data_attr_4").show();
	}else{
		$("#data_attr_4").hide();
	}
	
	if(coating!=0){
		lens_price = Number(lens_price).add(coating_price[coating][1]);
		coating_p = "$"+coating_price[coating][1];
		$("#deta_lenst_coatinc").html(coating_name[coating]);
		$("#deta_lenst_coatinc_p").html(coating_p);
		$("#data_attr_5").show();
	}else{
		$("#data_attr_5").hide();
	}

	if(bluelight==1){
		lens_price = Number(lens_price).add(bluelight_price[1]);
		bluelight_p = "$"+bluelight_price[1];
		$("#data_attr_6").show();
		$("#deta_lenst_bluelight").html('YES');
		$("#deta_lenst_bluelight_p").html(bluelight_p);
	}else{
		$("#data_attr_6").show();
		$("#deta_lenst_bluelight").html('NO');
		$("#deta_lenst_bluelight_p").html('');
	}

	$("#lens_price").html(toDecimal2(lens_price));
	$("#total").html(toDecimal2(Number(frame_price).add(lens_price)));

}


/*打开reading的选项面板*/
function show_reading_box(){
	$("#reading_box").modal();
}

/*reading选择*/
$("body").on("click",".reading_btn",function(){
	readers = $(this).attr('readers');
	$("#reading_box").modal('hide');
	$(".collapse").collapse('hide');
	$("#step_"+2+"_card").removeClass('disabled_card');
	$("#step"+2+"_div_box").addClass('show');
	changesetp(2);
})

/*打开新面板*/
function show_one_collapse(i){
	if(i==2){
		if(usage==3 || lenstype==7){
			removeBluelight(2);//Bifocal without Bluelight
		}else{
			unlockBluelight();
		}
		if(usage==2){
			show_reading_box();
			return false;
		}else if(usage==20){
			i = 3;
			clear_stpe_2();
		}else if(usage==30){
			clear_stpe_2();
			//$(".collapse").collapse('hide');
			$("#lens-add-cart").click();
			return false;
		}else{
			if($("#prescription").val()!=0){
				prescription = $("#prescription").val();
				if(prescription_list[prescription]){
					var pre_r = prescription_list[prescription];
					change_prescription(pre_r);
				}
			}
		}
	}
	if(i==3){
		change_lenstype();
	}	
	if(i==4){
		change_lensindex();
	}
	$(".collapse").collapse('hide');
	$("#step_"+i+"_card").removeClass('disabled_card');
	$("#step"+i+"_div_box").addClass('show');
	
	setTimeout(function(){
		var container = $("#lens_box");
		//console.log(i);
		if(i==5){
			var scrollTo = $("#step_"+i+"_card");
			//var scrollTo = $("#step_1_card");
			var st =  scrollTo.offset().top - container.offset().top + container.scrollTop();
			container.animate({scrollTop:(st-50)+"px"}, 200);
			//container.scrollTop((st-50));
		}
	},500)
	changesetp(i);
}

/*根据已选修改处方选项*/
function changesetp(i){
	if(i==2){
	
		if(readers==1 && usage==2){
			$("#prescription_box").hide();
			$("#power_box").show();
		}else{
			$("#prescription_box").show();
			$("#power_box").hide();
		}

		if(usage==1 || usage==2 || usage==5 || usage==20){
			$("#more-option-box").show();
			nearpd = 0; 
			$(".sr-near-pd-checkbox").removeClass('nearpd-check'); 
			$(".near-pd-ul").hide();
		}else{
			prism=0;
			$("#prime-checkbox").removeClass("prime-check");
			$("#more-option-box").hide();
		}
		if(usage==1 || usage==20){
			$("#od_add").val(0);
			$("#os_add").val(0);
			$(".sr-add").addClass('sr-disabled');
			$(".sr-add").find('select').addClass('disabled').attr("disabled","disabled");
		}else{
			$(".sr-add").removeClass('sr-disabled');
			$(".sr-add").find('select').removeClass('disabled').removeAttr("disabled");
		}

		if(usage==3){
			$("#step2_div_box .boc").hide();
		}else{
			$("#step2_div_box .boc").show();
		}

	}
}

function s_price(ps,ltk=''){  
	//console.log(ps);
	if(ltk==1){
		var oprice = ps[3];
		var price = ps[4];
		if(price==0){
			return "FREE";
		}else{
			if(oprice!=0){
				var pstr = "<del>($"+oprice+")</del>&nbsp;&nbsp;<span class='mk-price'>+$"+price+"</span>";			
			}else{
				var pstr = '+$'+price;			
			}
			if(ps[5]){
				pstr = pstr+"<span class='lensoff'>"+ps[5]+"</span>";
			}
			return pstr;
		}
	}else{
		var oprice = ps[0];
		var price = ps[1];
		if(price==0){
			return "FREE";
		}else{
			if(oprice!=0){
				var pstr = "<del>($"+oprice+")</del>&nbsp;&nbsp;<span class='mk-price'>+$"+price+"</span>";				
			}else{
				var pstr = '+$'+price;			
			}
			if(ps[2]){
				pstr = pstr+"<span class='lensoff'>"+ps[2]+"</span>";
			}
			return pstr;
		}

	}
}

/*add to cart*/
$("body").on("click", "#lens-add-cart", function (e) {	
	for (var i=1; i<=5; i++){ 
		if(!eval("check_step"+i+"_val()")){
			show_one_collapse(i);
			return false;
		}
	}

	$.MsgBox.Loading();
	setTimeout(function(){
	  $.ajax({
	    url:'/cart/add_cart',
	    type: "POST",
	    data: ({
	    	'goodsid':goodsid,
	    	'color':this_color,
	    	'login_key':login_key,
	    	'usage':usage,
	    	'lenstype':lenstype,
	    	'lenstype_color':lenstype_color,
	    	'lensindex':lensindex,
	    	'coating':coating,
	    	'pdkey':pdkey,
	    	'nearpd':nearpd,
	    	'prism':prism,
	    	'rxkey':rxkey,
	    	'rximg':rximg,
	    	'od_sph':$("#od_sph").val(),
	    	'os_sph':$("#os_sph").val(),
	    	'od_cyl':$("#od_cyl").val(),
	    	'os_cyl':$("#os_cyl").val(),
	    	'od_axis':$("#od_axis").val(),
	    	'os_axis':$("#os_axis").val(),
	    	'od_add':$("#od_add").val(),
	    	'os_add':$("#os_add").val(),
	    	'pd':$("#pd").val(),
	    	'od_pd':$("#od_pd").val(),
	    	'os_pd':$("#os_pd").val(),
	    	'npd':$("#npd").val(),
	    	'birth_year':$("#birth_year").val(),
	    	'od_prismnum_v':$("#od_prismnum_v").val(),
	    	'os_prismnum_v':$("#os_prismnum_v").val(),
	    	'od_prismdir_v':$("#od_prismdir_v").val(),
	    	'os_prismdir_v':$("#os_prismdir_v").val(),
	    	'od_prismnum_h':$("#od_prismnum_h").val(),
	    	'os_prismnum_h':$("#os_prismnum_h").val(),
	    	'od_prismdir_h':$("#od_prismdir_h").val(),
	    	'os_prismdir_h':$("#os_prismdir_h").val(),
	    	'lens_comment':$("#lens_comment").val(),
	    	'rx_name':$("#rx_name").val(),
	    	'rximg':rximg,
	    	'bluelight':bluelight,
	    	'editlens':editlens,
	    	'cartid':cartid,
	    	'lensid':lensid,
	    	'cartprice':Number(frame_price).add(lens_price),
	    	'readers':readers,
	    	'power':power,
		}),
	    dataType: "json",
	    async: false, 
	    beforeSend:function(){},
	    success:function(res){
	    	if(res.code==1){
				setTimeout(function(){
					window.location.href="/cart.html";
				},200);
				try {
					fbq('track', 'AddToCart', {
						value: Number(frame_price).add(lens_price),
						currency: 'USD',
						content_ids: goodsdata['code'],
						content_type: 'product',
					},{eventID: res.eventID});

					gtag('event', 'add_to_cart', {
					  currency: 'USD',
					  value: Number(frame_price).add(lens_price),
					  items: [{
					    item_id: goodsdata['code']+'-'+this_color,
					    item_name: goodsdata['code'],
					    quantity: 1
					  }],
					});

					push_itemcf(TemporaryUid,4,goodsid);
					//console.log(dataLayer);
				}catch(e){}
	    	}else{
		    	$.MsgBox.Close();
				$.MsgBox.Alert("ERROR", res.info);
	    	}
	    },error:function(e){
	    	$.MsgBox.Close();
			$.MsgBox.Alert("ERROR", "ERROR, Please try again.");
	    }
	  });
	},100)

})

/*#lens_box scroll*/
$('#lens_box').scroll(function(){        
	/*
	var bst = $('#lens_box').scrollTop();
	$('#lens_goods_box').css('margin-top',bst+20);
	$('#lens_left_close').css('top',bst);
	var lens_goods_box_h = $('#lens_goods_box').height();
	var lens_box_h = $('#lens_box').height();
	if(lens_goods_box_h > lens_box_h){
		$('#lens_goods_box').css('margin-top',(bst-80));
	}
	*/
	//console.log(lens_goods_box_h+'_'+lens_box_h);
});



/*upload prescription image*/

$(".click-upbtn").click(function(){
	$("#upload_file").click();
	var mainContainer = $('#lens_box');
	scrollToContainer = mainContainer.find('#upload_file');
	mainContainer.scrollTop(
		scrollToContainer.offset().top - mainContainer.offset().top + mainContainer.scrollTop()
	);
})

document.querySelector('#upload_file').addEventListener('change', function () {
	  var that = this;
	  lrz(that.files[0], {
	    width: 1200,     //图片最大不超过的宽度，默认为原图宽度，高度不设时会适应宽度。
	    quality:0.8     //图片压缩质量，取值 0 - 1，默认为0.7
	    //fieldName {String} 后端接收的字段名，默认：file
	  }).then(function (rst) {
	    up_rximg(rst);
	  });
});

function up_rximg(rst){
  $.MsgBox.Loading();
  setTimeout(function(){
	  $.ajax({
	    url:'/lens/up_rximg',
	    type: "POST",
	    data: ({rximg:rst.base64}),
	    dataType: "json",
	    async: false, 
	    beforeSend:function(){},
	    success:function(res){
	    	$.MsgBox.Close();
	    	//console.log(res);
	    	if(res.code==1){
		    	setRximg(res.url);
	    	}else{
				$.MsgBox.Alert("ERROR", res.info);
	    	}
	    },error:function(e){
	    	$.MsgBox.Close();
			$.MsgBox.Alert("ERROR", "Image upload failed, Please try again.");
	    }
	  });
  },200)
}   

function setRximg(url){
    //console.log(url);
    $("#rximg").show();
	$("#uppic-btn").css("background","url("+url+")");
	$("#uppic-btn").css("background-size","100% 100%");
	rximg = url;
}

function clearRximg(){
    //console.log('123');
    $("#rximg").hide();
	$("#uppic-btn").css("background","url(/public/static/img/icons/upload.png) center no-repeat");
	$("#uppic-btn").css("background-size","100% 100%");
	rximg = '';
}

function init_lenses(){
	cartid = cartinfo.id;
	lensid = lensinfo.id;
	usage = lensinfo.usage;
	
	readers = lensinfo.readers;
	power = lensinfo.power;

	$("#step1_li_"+usage).addClass('lens_k_choose');
	$("#step_1_cn").html(usage_name[usage]+"&nbsp;&nbsp;<span class='un_line'>&nbsp;EDIT&nbsp;</span>");
	if(usage!=20 && usage!=30){
		//$("#prescription").val('0');
		change_prescription(lensinfo);
		$(".collapse").collapse('hide');
		$("#step_2_card").removeClass('disabled_card');
		$("#step2_div_box").addClass('show');
		if(usage==2 && readers==1){
			$("#prescription_box").hide();
			$("#power_box").show();	
			$(".power_v_choose").removeClass("power_v_choose");
			$(".power_v[power = '"+power+"']").addClass("power_v_choose");	
		}
	}
	if(lensinfo.bluelight==1){
		chooseBluelight();
	}
	count_price();
}


$(".bluelight-btn").click(function(){
	if(bluelight==0 && usage!=3 && lenstype!=7 && lenstype_color!=52){
		chooseBluelight();
	}else{
		removeBluelight(1);
	}
	count_price();
})

function chooseBluelight(){
	bluelight=1;
	$(".bluelight-btn").addClass("bluelight-check");
	$(".bluelight-add").html('Added');
}
function removeBluelight(lock){
	bluelight=0;
	$(".bluelight-btn").removeClass("bluelight-check");
	$(".bluelight-add").html('ADD');
	if(lock==2){
		lockBluelight();
	}
	count_price();
}

function lockBluelight(){
	$(".bluelight-btn").css('opacity','0.5');
	$(".bluelight-btn .lock_tips").show();
	$(".bluelight-btn .lens_k_price").hide();
}

function unlockBluelight(){
	$(".bluelight-btn").css('opacity','1');
	$(".bluelight-btn .lock_tips").hide();
	$(".bluelight-btn .lens_k_price").show();
}


//选择外渐进时，提示升级为内渐进
function confimProgressive(){
	if(usage==4){
		var pp_cj = toDecimal2((lenstype_price[5][lenstype][4] - lenstype_price[4][lenstype][4])+(lensindex_price[5][lensindex][1]-lensindex_price[4][lensindex][1]));
		$("#pp_cj").html(pp_cj);
		$("#confimProgressiveModal").modal();
	}else{
		cartOverspread();
	}
}

$(".upgrade_progressive_cancel").click(function(){
	$("#confimProgressiveModal").modal('hide');
	cartOverspread();
})

$(".upgrade_progressive_btn").click(function(){
	$("#confimProgressiveModal").modal('hide');
	usage = 5; count_price();
	$("#step1_div_box .lens_k_choose").removeClass("lens_k_choose");
	$("#step1_div_box #step1_li_5").addClass("lens_k_choose");
	$("#step_1_cn").html(usage_name[usage]+"&nbsp;&nbsp;<span class='un_line'>&nbsp;EDIT&nbsp;</span>");
	cartOverspread();
})

function cartOverspread(){
	$("#lensbox_left").hide();
	$("#lensbox_right").addClass('col-full bounceInRight');
	$("#edit-again").show();
}



function cartRecover(){
	$("#lensbox_left").show();
	$("#lensbox_right").removeClass('col-full bounceInRight');
	$("#edit-again").hide();
}

$("#edit-again").click(function(){
	cartRecover();
})