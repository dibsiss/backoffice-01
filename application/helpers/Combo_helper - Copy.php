<?php

function cmb_suppliyer() {
    $ci = get_instance();
    $statusUser = $ci->session->userdata('hak_user');
    $idUserGlobal = $ci->session->userdata('owner');
    $idSupliyer = $ci->session->userdata('id_supplier');
    //get option for superadmin and gudang 
    //beda di superadmin dan gudang hanya jika dia gudang maka tampil tambahan suppliyer adalah dari superadmin
    $optionUmum = $ci->db->get_where('mst_supliyer')->result();
    $umum = '';
    $cmb = "<option value=''></option>";
    $umum .= "<optgroup label='Supliyer'>";
    foreach ($optionUmum as $optUmum) {
        ($optUmum->id_supliyer == $idSupliyer) ? $selected = 'selected' : $selected = '';
        $umum .= "<option value='$optUmum->id_supliyer' $selected>$optUmum->nama</option>";
    }
    $umum .= " </optgroup>";
    switch ($statusUser) {
        case 'superadmin':
            $cmb .= $umum;
            break;
        case 'gudang':
            ($idSupliyer=='superadmin')?$selectedSuperadmin='selected':$selectedSuperadmin='';
            $cmb .= "<optgroup label='Superadmin'>";
            $cmb .= "<option value='superadmin' $selectedSuperadmin>Superadmin</option>";
            $umum .= " </optgroup>";
            $cmb .= $umum;
            break;
        case 'toko':
            $idGudang = $ci->db->query("select a.id_gudang,b.nama from mst_toko a
            inner join mst_gudang b on a.id_gudang = b.id_gudang where a.id_toko = '$idUserGlobal'")->result();
            $cmb .= "<optgroup label='Gudang'>";
            foreach ($idGudang as $ig) {
                ($ig->id_gudang==$idSupliyer)?$selected = 'selected' : '';
                $cmb .= "<option value='$ig->id_gudang'>$ig->nama</option>";
            }
             $cmb .= " </optgroup>";
            break;
        default :
            break;
    }
    return $cmb;
}

function cmb_jenis_non_fisik(){
    $ci = get_instance();
    $getJenis = $ci->db->get('mst_jenis_non_fisik')->result();
    $cmb = "<option value=''></option>";
    foreach ($getJenis as $gt) {
        $cmb .= "<option value='$gt->id_jenis_non_fisik' $selected>$gt->nama</option>";
    }
    return $cmb;
}

function cmb_customer() {
    $ci = get_instance();
    $idUserGlobal = $ci->session->userdata('owner');
    $idCustomer = $ci->session->userdata('id_customer');
    $getCustomer = $ci->db->get_where('mst_customer', array('id_toko' => $idUserGlobal))->result();
    $cmb = "<option value=''></option>";
    foreach ($getCustomer as $gt) {
        ($gt->id_customer == $idCustomer) ? $selected = 'selected' : $selected = '';
        $cmb .= "<option value='$gt->id_customer' $selected>$gt->nama</option>";
    }
    return $cmb;
}
function cmb_jenis_retur() {
    $ci = get_instance();
    $statusUser = $ci->session->userdata('hak_user');
    $idUserGlobal = $ci->session->userdata('owner');
    $idRetur = $ci->session->userdata('id_retur');
    $getRetur = $ci->db->get_where('mst_retur', array('owner' => $statusUser))->result();
    $cmb = "<option value=''></option>";
    foreach ($getRetur as $gt) {
        ($gt->id_retur == $idRetur) ? $selected = 'selected' : $selected = '';
        $cmb .= "<option value='$gt->id_retur' $selected>$gt->status</option>";
    }
    return $cmb;
}

function cmb_pengiriman() {
    $ci = get_instance();
    $statusUser = $ci->session->userdata('hak_user');
    $idUserGlobal = $ci->session->userdata('owner');
    $id_tujuan = $ci->session->userdata('id_tujuan');
    $cmb = "<option value=''></option>";
    switch ($statusUser) {
        case 'superadmin':
            $gudang = $ci->db->get('mst_gudang')->result();
            $cmb .= "<optgroup label='Gudang'>";
            foreach ($gudang as $gd) {
                ($id_tujuan == $gd->id_gudang) ? $selected = 'selected' : $selected = '';
                $cmb .= "<option value='$gd->id_gudang' $selected>$gd->nama</option>";
            }
            $cmb .= " </optgroup>";
            break;
        case 'gudang':
            $toko = $ci->db->get_where('mst_toko', array('id_gudang' => $idUserGlobal))->result();
            foreach ($toko as $tk) {
                ($id_tujuan == $tk->id_toko) ? $selected = 'selected' : $selected = '';
                $cmb .= "<option value='$tk->id_toko' $selected>$tk->nama</option>";
            }
            break;
        case 'toko':
            //get id gudang yang masih dalam satu toko
            $gudangSejenis = $ci->session->userdata('toko_segudang');
            $toko = $ci->db->get_where('mst_toko', array('id_gudang' => $gudangSejenis))->result();
            foreach ($toko as $tk) {
                ($id_tujuan == $tk->id_toko) ? $selected = 'selected' : $selected = '';
                $cmb .= "<option value='$tk->id_toko' $selected>$tk->nama</option>";
            }
            break;
        default:
            break;
    }
    return $cmb;
}

function cmb_permintaan() {
    $ci = get_instance();
    $statusUser = $ci->session->userdata('hak_user');
    $idUserGlobal = $ci->session->userdata('owner');
    $id_tujuan = $ci->session->userdata('id_tujuan_permintaan');
    $cmb = "<option value=''></option>";
    switch ($statusUser) {
        case 'gudang':
            //jika dia gudang maka dia hanya bisa minta kepada superadmin
            ($id_tujuan=='superadmin')?$selected='selected':$selected='';
            $cmb .= "<option value='superadmin' $selected>Superadmin</option>";
            break;
        case 'toko':
            //get id gudang yang masih dalam satu toko
            $gudangSejenis = $ci->session->userdata('toko_segudang');
            $gudang = $ci->db->get_where('mst_gudang', array('id_gudang'))->result();
            $cmb .= "<optgroup label='Gudang'>";
            foreach ($gudang as $gd) {
                ($id_tujuan == $gd->id_gudang) ? $selected = 'selected' : $selected = '';
                $cmb .= "<option value='$gd->id_gudang' $selected>$gd->nama</option>";
            }
            $cmb .= " </optgroup>";
            //jika toko dia juga bisa meminta dari toko yang lain yang masih satu gudang dengan dia
            $cmb .= "<optgroup label='Toko Lain'>";
            $toko = $ci->db->get_where('mst_toko', array('id_gudang' => $gudangSejenis))->result();
            foreach ($toko as $tk) {
                ($id_tujuan == $tk->id_toko) ? $selected = 'selected' : $selected = '';
                $cmb .= "<option value='$tk->id_toko' $selected>$tk->nama</option>";
            }
            $cmb .= " </optgroup>";
            break;
        default:
            break;
    }
    return $cmb;
}

?>