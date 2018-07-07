<!--<style>
    .navbar-nav > .notifications-menu > .dropdown-menu, .navbar-nav > .messages-menu > .dropdown-menu, .navbar-nav > .tasks-menu > .dropdown-menu {
    width: unset !important;
}
</style>-->
<script>
    $(function () {
//        manualCheck();
    });
    function getNotifKirim() {
        $.post("<?php echo site_url(); ?>/umum/getNotifKirim/", {}, function (obj)
        {
            $('#notif-kirim').html(obj);
        });
    }
    function getNotifKirimSumber() {
        $.post("<?php echo site_url(); ?>/umum/getNotifKirimSumber/", {}, function (obj)
        {
            $('#notif-kirim-sumber').html(obj);
        });
    }
    function getNotifReject() {
        $.post("<?php echo site_url(); ?>/umum/getNotifReject/", {}, function (obj)
        {
            $('#notif-reject').html(obj);
        });
    }
    function getNotifPermintaan() {
        $.post("<?php echo site_url(); ?>/umum/getNotifPermintaan/", {}, function (obj)
        {
            $('#notif-permintaan').html(obj);
        });
    }
    function getNotifRetur() {
        $.post("<?php echo site_url(); ?>/umum/getNotifRetur/", {}, function (obj)
        {
            $('#notif-retur').html(obj);
        });
    }
    function manualCheck() {
        getNotifKirim();
        getNotifPermintaan();
        getNotifKirimSumber();
        getNotifReject();
        getNotifRetur();
    }
//    setInterval(function () {
//        manualCheck();
//    }, 50000);
</script>