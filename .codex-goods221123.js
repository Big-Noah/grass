<!DOCTYPE html>
<html lang="en">
<head> 
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico">
<meta name="description" content="">
<meta name="author" content="">
<title>MuukalOptical - 404</title>
<!-- main css -->
<style type="text/css">

  @import url('https://fonts.googleapis.com/css?family=Viga'); 
  *{ margin: 0; }
  body {
      padding-top: 0;
      font-family: 'Roboto', sans-serif;
      line-height: 1.8;
  }
  .bg-404{
  padding: 0;
  margin: 0;
  /*background: url(../images/bg-img/background.jpg)no-repeat 0px 0px #262b30;*/
  background: #efeff4;
  background-attachment: fixed;
  background-position: 50% 50%;
  background-size: cover;
  }

  /*--header--*/
  .agile-errheader h1 {
  font-size: 50px;
  font-weight: 500;
  text-align: center;
  text-transform: uppercase;
  letter-spacing: 2px;
  padding: 1em 0 1em;
  color: #000000; 
  `
  }
  .agile-errmain {
  width:100%;
  margin: 0 auto;
  }
  .agile-errmain h2 {
  font-size: 180px;
  font-weight: 500;
  text-align: center;
  letter-spacing: 10px;
  padding: 0 0 0;
  color: #ff565c; font-family: 'Viga', sans-serif;
  }
  .agile-errmain p {
  font-size: 25px;
  font-weight: 500;
  text-align: center;
  text-transform: capitalize;
  letter-spacing: 2px;
  color: #000000;
  }
  .agile-errmain span {
  font-size: 20px;
  font-weight: 500;
  text-align: center;
  text-transform: capitalize;
  letter-spacing: 1px;
  color: #000000;
  display: block;
  padding: 1em 0 1em;
  }
  .agile-back {margin:2em auto; text-align:center}
  button.submit {
  font-size:16px;
  font-weight: 500;
  text-align: center;
  text-transform: capitalize;
  letter-spacing: 2px;
  padding: 0.8em 3em;
  background: #ff565c;
  cursor: pointer;
  color: #ffffff;
  border: none;
  outline: none; border-radius:100px;
  }


  button.submit:hover { background:#000000; color:#000; font-weight:600}

  button.help {font-size: 17px;font-weight: 500;text-align: center;text-transform: capitalize;letter-spacing: 2px;padding: 0.8em 0.8em;background: rgba(241, 234, 6, 0.31);width:50%;cursor:pointer;color:#000000;border:none;outline: none;
  }
  .copy-right2 { text-align:center; font-size:13px; color:#000000}

  @media (max-width:575px){
  .agile-errmain { width:100%}
  .agile-errheader h1 { font-size:30px}
  .agile-errmain p {font-size:20px; padding:.5em 0 .5em}
  .agile-errmain h2 {font-size:100px}
  }

  @media (max-width:480px){
  .agile-errmain { width:100%}
  .agile-errheader h1 { font-size:20px}
  .agile-errmain p {font-size:15px; padding:.5em 0 .5em}
  .agile-errmain h2 {font-size:100px}
  }

  @media (max-width:375px){.
  copy-right2 {font-size:12px}.agile-errmain span {font-size:16px}
  .agile-errmain p {font-size:13px; padding:.5em 0 .5em}
  .copy-right2 { font-size:12px;}
  }
</style>

<!-- main css -->
</head>
<body class="bg-404">
<div class="container">
  <section class="agile-error"> 
    <!--728x90-->
    <div class="agile-errheader">
      <h1>interactive <span>error</span> page</h1>
    </div>
    <div class="agile-errmain"> 
      <!--728x90-->
      <h2>404</h2>
      <p>oops!sorry we can't find this page.</p>
      <span>either something went wrong or the page doesn't exist anymore.</span> </div>
    <div class="agile-home"> 
      <!--728x90-->
      <form method="post">
        <div class="agile-back">
          <button class="submit" type="button" onClick="location.href='/'">back home </button>
        </div>
      </form>
    </div>
    <div class="copy-right2">
      <p>Copyright © 2020 Muukal Online Optical Store. All Rights Reserved</p>
    </div>
    <div class="clear"></div>
  </section>
</div>
<script src="https://www.muukal.com/public/static/assets/js/vendor/jquery-1.12.4.min.js"></script>
<script type="text/javascript">

    var ref_url = '';  
    if (document.referrer.length > 0) {  
      ref_url = document.referrer;  
    }  
    try {  
      if (ref_url.length == 0 && opener.location.href.length > 0) {  
       ref_url = opener.location.href;  
      }  
    } catch (e) {} 
    console.log(ref_url);

    var this_url = window.location.href;
    console.log(this_url);

    $.ajax({
        url: '/napi/recdata',
        data: ({
            'datakey':'ErrorPage',
            'source':1,
            'mkupk':1,
            'this_url':this_url,
            'ref_url':ref_url,
        }),
        type: 'post',
        async: true,
        success: function (res) {
          setTimeout(function(){window.location.href = "https://www.muukal.com";},1500)
        },error: function (e) {
          setTimeout(function(){window.location.href = "https://www.muukal.com";},1500)
        }
    });  

    setTimeout(function(){
      window.location.href = "https://www.muukal.com";
    },3500)
</script>
</body>
</html>