$(function(){

  var windows_w = $("#tryonModal").width();
  if(windows_w > 920){windows_w=920;}
  $("#tryon_block, #tryon_do_block,#tryon_mine").css("width",(windows_w-30));
  $(".tyyon_right_lb").css("width",(windows_w-420));
  if(windows_w < 800){
    $(".try_info").hide();
  }

  $(".eye_icon").Tdrag({
      scope:"#tryon_box"
  });
  $("#tryon_glass").Tdrag({
      scope:"#tryon_box"
  });

}) 


function get_tryon(){
    setTimeout(function(){
      $.ajax({
        url:'/userajax/get_tryon',
        type: "POST",
        data: ({
          'from':1,
        }),
        dataType: "json",
        async: false, 
        beforeSend:function(){},
        success:function(res){
          //console.log(res);
          if(res.code==1){
            var list = res.list;
            $.each(list, function(i, val) {
              var t_html= '<div class="tyron_img_box"><img i_w="'+val['img_w']+'" i_h="'+val['img_h']+'" f_width="'+val['frame_width']+'" f_top="'+val['frame_top']+'" f_left="'+val['frame_left']+'" f_rotate="'+val['frame_rotate']+'" class="try_u_pic" src="'+val['pic']+'"><div tid="'+val['id']+'" class="del_tryon iconfont icon-close"></div></div>';
              $("#try_pic_box").prepend(t_html);
            });
          }
        },error:function(e){
          $.MsgBox.Close();
        }
      });
    },200)
}

$("body").on("click", ".cv-btn", function (e) { 
  //console.log('x_oss_process'+x_oss_process);
  var src = ossurl+'goods/'+gid+'/tryon/'+$(this).attr('cv')+'.png'+x_oss_process;
  $("#tryon_glass").attr('src',src);
})


$("body").on("click", ".try_u_pic,.try_sys_pic", function (e) { 
  var src = $(this).attr("src");
  var i_w = $(this).attr("i_w");
  var i_h = $(this).attr("i_h");
  var f_width = $(this).attr("f_width");
  var f_top = $(this).attr("f_top");
  var f_left = $(this).attr("f_left");
  var f_rotate = $(this).attr("f_rotate");
  
  var t_f_width = (f_width*tryon_size[0])/i_w;
  var t_f_top = (f_top*tryon_size[0])/i_w;
  var t_f_left = (f_left*tryon_size[0])/i_w;

  $("#tryon_img").attr("src",src);
  $("#tryon_glass").css('width',t_f_width);
  $("#tryon_glass").css({"top":t_f_top,"left":t_f_left});
  $("#tryon_glass").css("transform","rotate("+f_rotate+"deg)");
  $("#try_pic_block").hide();
})

//choose an image
function selectImg(file) {
    if (!file.files || !file.files[0]){
        return;
    }

    var Orientation = 0;
    EXIF.getData(file.files[0], function() {
        var exifData = EXIF.getAllTags(this);
        //console.log(exifData);
        if(exifData && exifData.Orientation==6){
          Orientation = 6;
        }
        var reader = new FileReader();
        reader.onload = function (evt) {
            //console.log(evt.total);
            if(evt.total>5242880){
              $.MsgBox.Alert("INFO","Please make sure to select an image (jpeg, png) and its file size is less than 5MB");
              return false;
            }
            var replaceSrc = evt.target.result;
            if(Orientation == 6){
              choose_img.cropper('replace', replaceSrc,true);
            }else{
              choose_img.cropper('replace', replaceSrc,false);
            }
            $("#tryon_box,#tryon_glass").hide();
            $("#choose_box").show();
            $(".try_step_3,.try_step_4").hide();
            $("#tryon_block").show();
            $(".try_step_2").show();
        }
        setTimeout(function(){
          reader.readAsDataURL(file.files[0]);
        },200);
        init_cropper()
    });
}

function init_cropper(){
    choose_img.cropper({
        aspectRatio: 35/41,//默认比例
        guides: true,  //裁剪框的虚线(九宫格)
        autoCropArea: 0.85,  //0-1之间的数值，定义自动剪裁区域的大小，默认0.8
        movable: true, //是否允许移动图片
        dragCrop: false,  //是否允许移除当前的剪裁框，并通过拖动来新建一个剪裁框区域
        movable: true,  //是否允许移动剪裁框
        resizable: true,  //是否允许改变裁剪框的大小
        zoomable: true,  //是否允许缩放图片大小
        mouseWheelZoom: true,  //是否允许通过鼠标滚轮来缩放图片
        touchDragZoom: false,  //是否允许通过触摸移动来缩放图片
        rotatable: true,  //是否允许旋转图片
        crop: function(e) {
        }
    });
}

//旋转
$(".rotate-btn").on("click",function () {
    choose_img.cropper("rotate", 90);
});

//镜像
var flagX = true;
$(".scaleX-btn").on("click",function () {
    if(flagX){
        choose_img.cropper("scaleX", -1);
        flagX = false;
    }else{
        choose_img.cropper("scaleX", 1);
        flagX = true;
    }
    flagX != flagX;
});

$(".sureCut").on("click",function () {
    if (choose_img.attr("src") == null ){
        return false;
    }else{
        var cas = choose_img.cropper('getCroppedCanvas');//获取被裁剪后的canvas
        var base64url = cas.toDataURL('image/png'); //转换为base64地址形式
        compress(base64url,0.35,function(res){//压缩后使用
          //console.log(base64url);
          tryon_img.prop("src",res);
          closeTailor();//关闭裁剪框
          detectFace(res);//脸部检测
          $(".try_step_2").hide();
          $(".try_step_1,.try_step_3").show();
          //$(".eye_icon").show();
        });
    }
});

function sureGlass(){
  var try_pd = parseInt($("#try_pd").val());
  //ponit_center
  left_point = {
          "x": parseInt($("#left_pupil").css("left"))+eye_icon_radius,
          "y": parseInt($("#left_pupil").css("top"))+eye_icon_radius,
  };
  right_point = {
          "x": parseInt($("#right_pupil").css("left"))+eye_icon_radius,
          "y": parseInt($("#right_pupil").css("top"))+eye_icon_radius,
  };

  var eye_x_px = Math.abs(right_point.x - left_point.x);
  var galss_x_px = ((frame_width*eye_x_px)/(lens_width+bridge_width))*1.1;
  $("#tryon_glass").css('width',galss_x_px);

  var left_center = ((right_point.x-left_point.x)/2)+left_point.x;
  var top_center = ((right_point.y-left_point.y)/2)+left_point.y;

  var left_deviation = left_center - (galss_x_px/2);
  var top_deviation = top_center - (((108*galss_x_px)/200)/2);

  $("#tryon_glass").css({"top":(top_deviation),"left":(left_deviation)});

  var rotate = angle(left_point,right_point);
  f_rotate = rotate;
  $("#tryon_glass").css("transform","rotate("+rotate+"deg)");
  $(".eye_icon").hide();
  $("#tryon_glass").show();

  $(".try_step_3").hide();
  $(".try_step_4").show();
}


$(".sureGlass").on("click",function () {
  sureGlass();
})


$(".saveTryon").on("click",function () {
  var dataSrc = $("#tryon_img").attr("src");
  var try_title = $("#try_title").val();
  var f_width = $("#tryon_glass").width();
  var f_top = parseInt($("#tryon_glass").css("top"));
  var f_left = parseInt($("#tryon_glass").css("left"));
  var img_w = $("#tryon_img").width();
  var img_h = $("#tryon_img").height();
  $.MsgBox.Loading();
  setTimeout(function(){
    $.ajax({
      url:'/userajax/save_tryon',
      type: "POST",
      data: ({
        'try_title':try_title,
        'dataSrc':dataSrc,
        'f_width':f_width,
        'f_top':f_top,
        'f_left':f_left,
        'f_rotate':f_rotate,
        'img_w':img_w,
        'img_h':img_h,
        'from':1,
      }),
      dataType: "json",
      async: false, 
      beforeSend:function(){},
      success:function(res){
        $.MsgBox.Close();
        if(res.code==1){
          $.MsgBox.Alert("INFO", 'SAVE SUCCESS');
          var t_html= '<img i_w="'+img_w+'" i_h="'+img_h+'" f_width="'+f_width+'" f_top="'+f_top+'" f_left="'+f_left+'" f_rotate="'+f_rotate+'" class="try_u_pic" src="'+dataSrc+'">';
          $("#try_pic_box").prepend(t_html);
          $(".try_step_4").hide();
        }else{
          $.MsgBox.Alert("ERROR", res.info);
        }
      },error:function(e){
        $.MsgBox.Close();
        $.MsgBox.Alert("ERROR", "ERROR, Please try again.");
      }
    });
  },200)





})


function angle(start,end){
    var diff_x = end.x - start.x,
        diff_y = end.y - start.y;
    return 360*Math.atan(diff_y/diff_x)/(2*Math.PI);
}


function detectFace(base64Image) {
    $("#tryon_glass").show();
    $.MsgBox.Loading();
    $("<img/>").attr("src", $("#tryon_img").attr("src")).load(function() {
        realWidth = this.width;
        realHeight = this.height;
        let imageData = facepp.dataURItoBlob(base64Image);
        let attributes = 'eyestatus,emotion,ethnicity,beauty,mouthstatus,eyegaze,skinstatus';
        let dataDic = {
        'image_file':imageData,
        //'image_base64':base64Image,
        'return_landmark':1,
        'return_attributes':attributes
        };
        facepp.detectFace(dataDic,function success(e){
          //console.log(e);
          $.MsgBox.Close();
          if(e.faces){
            var left_eye = e.faces[0].landmark.left_eye_pupil;
            var right_eye = e.faces[0].landmark.right_eye_pupil;

            left_realX = (tryon_size[0]/realWidth)*left_eye.x;
            left_realY = (tryon_size[1]/realHeight)*left_eye.y;
            right_realX = (tryon_size[0]/realWidth)*right_eye.x;
            right_realY = (tryon_size[1]/realHeight)*right_eye.y;

            $("#left_pupil").css({"top":(left_realY-eye_icon_radius),"left":(left_realX-eye_icon_radius)});
            $("#right_pupil").css({"top":(right_realY-eye_icon_radius),"left":(right_realX-eye_icon_radius)});
            sureGlass();
          }
        },function failed(e){
          sureGlass();
          $.MsgBox.Close();
          recdata('TryonError',1);
        });
    });
}

function closeTailor() {
    $("#tryon_box").show();
    $("#choose_box").hide();
    if($(window).width() > 1024){
      $(".eye_icon").show();
    }
    choose_img.cropper('clear');
    choose_img.cropper('destroy');
}

$(".change-tryon-pg").click(function(){
  window.location.reload();
})



$("#size_b").click(function(){
    tg_size("b");
})
$("#size_s").click(function(){
    tg_size("s");
})
function tg_size(key){
  var tw = $("#tryon_glass").width();
  if(key=='b'){var nw  = tw+3;}
  if(key=='s'){var nw  = tw-3;}
  $("#tryon_glass").width(nw);
}

$("#rotate_l").click(function(){
    tg_rotate("l");
})
$("#rotate_r").click(function(){
    tg_rotate("r");
})
function tg_rotate(key){
    var transform = $("#tryon_glass").css("transform");
    if(key=='r'){var deg  = getmatrix(transform)+1;}
    if(key=='l'){var deg  = getmatrix(transform)-1;}
    $("#tryon_glass").css({
        "-webkit-transform":"rotate("+deg+"deg)",
        "-moz-transform":"rotate("+deg+"deg)",
        "-ms-transform":"rotate("+deg+"deg)",
        "-o-transform":"rotate("+deg+"deg)",
        "transform":"rotate("+deg+"deg)",
    });
}
$("#move_t").click(function(){
    tg_move("t");
})
$("#move_r").click(function(){
    tg_move("r");
})
$("#move_b").click(function(){
    tg_move("b");
})
$("#move_l").click(function(){
    tg_move("l");
})
function tg_move(key){
  if(key=='t' || key=='b'){
    var top = parseInt($("#tryon_glass").css("top"));
    if(key=='t'){var ntop  = top-3;}
    if(key=='b'){var ntop  = top+3;}
    $("#tryon_glass").css("top",ntop);
  }
  if(key=='l' || key=='r'){
    var left = parseInt($("#tryon_glass").css("left"));
    if(key=='l'){var nleft  = left-3;}
    if(key=='r'){var nleft  = left+3;}
    $("#tryon_glass").css("left",nleft);
  }

}




function getmatrix(transform){
  var values = transform.split('(')[1].split(')')[0].split(',');
  var a = values[0];
  var b = values[1];
  var c = values[2];
  var d = values[3];

    var aa=Math.round(180*Math.asin(a)/ Math.PI);
    var bb=Math.round(180*Math.acos(b)/ Math.PI);
    var cc=Math.round(180*Math.asin(c)/ Math.PI);
    var dd=Math.round(180*Math.acos(d)/ Math.PI);
    var deg=0;
    if(aa==bb||-aa==bb){
        deg=dd;
    }else if(-aa+bb==180){
        deg=180+cc;
    }else if(aa+bb==180){
        deg=360-cc||360-dd;
    }
    return deg>=360?0:deg;
}



$("body").on("click", ".del_tryon", function (e) { 
    $.ajax({
      url:'/userajax/del_tryon',
      type: "POST",
      data: ({
        'mkupk':1,
        'tryon_id':$(this).attr('tid'),
      }),
      dataType: "json",
      async: true, 
      beforeSend:function(){},
      success:function(res){
      },error:function(e){
      }
    });
    $(this).parent().remove();
})
 
