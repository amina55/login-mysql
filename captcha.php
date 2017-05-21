<script>
    function refreshCaptcha() {
    $("#captcha_code").attr('src', 'captcha_code.php');
}
</script>
<div class="form-group ">
    <div class="col-sm-12">
        <img id="captcha_code" src="captcha_code.php" />
        <a onClick="refreshCaptcha();"><img class="btnRefresh" width="50px" height="50px" src="images/refresh.png"></img></a>
    </div>
</div>