<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>:: Aplikasi Pendataan Rumah Tidak Layak Huni (RUTILAHU)::</title>
    <link rel="icon" href="<?=base_url()?>/assets/images/logo_sm_2_mr_1.png" type="image/png">    
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?=base_url();?>assets/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <style type="text/css">    
    body {
        background-color: #444;
        background: url(<?php echo base_url();?>assets/images/map.png);
		background-repeat:no-repeat;
		background-size:cover;		
    }
    .form-signin input[type="text"] {
        margin-bottom: 5px;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }
    .form-signin input[type="password"] {
        margin-bottom: 10px;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }
    .form-signin .form-control {
        position: relative;
        font-size: 16px;
        font-family: 'Open Sans', Arial, Helvetica, sans-serif;
        height: auto;
        padding: 10px;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }
    .vertical-offset-100 {
        padding-top: 100px;
    }
    .img-responsive {
    display: block;
    max-width: 100%;
    height: auto;
    margin: auto;
    }
    .panel {
    margin-bottom: 20px;
    background-color: rgba(255, 255, 255, 0.75);
    border: 1px solid transparent;
    border-radius: 4px;
    -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, .05);
    box-shadow: 0 1px 1px rgba(0, 0, 0, .05);
    }
    </style>
    <script src="<?=base_url();?>assets/js/jquery.min.js"></script>
    <script src="<?=base_url();?>assets/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        window.alert = function(){};
        var defaultCSS = document.getElementById('bootstrap-css');
        function changeCSS(css){
            if(css) $('head > link').filter(':first').replaceWith('<link rel="stylesheet" href="'+ css +'" type="text/css" />'); 
            else $('head > link').filter(':first').replaceWith(defaultCSS); 
        }
        $( document ).ready(function() {
          var iframe_height = parseInt($('html').height()); 
          window.parent.postMessage( iframe_height, '<?php echo base_url(); ?>');
        });
    </script>
</head>
<body>
        <script src="<?=base_url();?>assets/js/TweenLite.min.js"></script>
        <body>
            <div class="container">
                <div class="row vertical-offset-100">
                    <div class="col-md-4 col-md-offset-4">
                        <div class="panel panel-default">
                            <div class="panel-heading">                                
                                <div class="row-fluid user-row">
                                    <img src="<?=base_url();?>assets/images/head-login.png" class="img-responsive" alt="Aplikasi Pendataan Rumah Tidak Layak Huni"/>
                                </div>
                            </div>
                            <div class="panel-body">
                                <form accept-charset="UTF-8" role="form" method="post" class="form-signin" action="<?=base_url()?>index.php/dashboard/Mainindex/process_login/0" >
                                    <fieldset>
                                        <label class="panel-login">
                                            <div class="login_result"></div>
                                        </label>
                                        <input class="form-control" placeholder="nama user" name="username" id="username" type="text">
                                        <input class="form-control" placeholder="kata kunci" name="password" id="password" type="password">
                                        <br></br>
                                        <input class="btn btn-lg btn-success btn-block" type="submit" id="login" value="Login »">
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
            </div>
</body>
</html>
