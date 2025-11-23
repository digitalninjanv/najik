<?php
include '../../config.php';
error_reporting(0);

/* Halaman ini tidak dapat diakses jika belum ada yang login(masuk) */
if(isset($_SESSION['email'])== 0) {
	header('Location: ../../index.php');
}

if( $_SESSION['level_id'] == "1" || $_SESSION['level_id'] == "2" ){
}else{
  echo "<script>alert('Maaf! anda tidak bisa mengakses halaman ini '); document.location.href='../admin/'</script>";
}

$master = "Tambah Order";
$dba = "tambahorder";
$ket = "| Nomor Hp - Alamat";
$ketnama = "Silahkan mengisi nama";

?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
 
  <?php
  include '../header.php';
  include '../sidebar.php';
  ?>



  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>

        <?php echo $master; ?>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Tables <?php echo $master; ?></a></li>
        <li class="active"><?php echo $master; ?></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tambah">
            Tambah
              </button>
          <!-- /.box -->


          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Master Data <?php echo $master; ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
            
            <div class="form-group">
              <a class="btn btn-primary" href="./">Lihat Data</a>
          </div>
            
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>

   <!-- Modal Tambah -->
<div class="modal fade" id="tambah" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo $master;?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <div class="modal-body">
          <form action="../../controller/<?php echo $dba;?>_controller.php?op=tambah" method="post"  enctype="multipart/form-data">
          <?php if( $_SESSION['level_id'] == "1"){ ?>
            <div class="form-group">
              <a class="btn btn-primary" href="./">Lihat Data</a>
          </div>
          <?php } ?>
          
          <div class="form-group">
            <label class="control-label" >Tanggal : </label>         
				<input type="text" class="form-control" name="created_at" value="<?php echo date("Y-m-d H:i:s");?>" />
				<small style="color:red;">tahun-bulan-tgl jam</small>
          </div>
          
          <div class="form-group">
            <label class="col-form-label">Jenis Order :</label>
            <select style="width: 100%;" class="form-control" name="order_kat_id" id="order_kat_id">
                <option value="0">-- Pilih Jenis Order --</option>
                <?php
                $sql = $conn->prepare("SELECT * FROM m_order_kat");
                $sql->execute();
                while($data = $sql->fetch()) {
                ?>  
                <option value="<?php echo $data['id']; ?>"><?php echo $data['nama']; ?></option>
                <?php } ?> 
            </select>
           </div>

            <div class="form-group">
                <label class="col-form-label">Sub Jenis Order :</label>
                <select style="width: 100%;" class="form-control" name="order_katsub_id" id="order_katsub_id">
                    <option value="0">-- Pilih Sub Jenis Order --</option>
                </select>
            </div>
            
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
            $('#order_kat_id').change(function() {
                var orderKatId = $(this).val();
            
                if(orderKatId) {
                    $.ajax({
                        type: 'POST',
                        url: 'get_suborder.php',
                        data: {order_kat_id: orderKatId},
                        success: function(response) {
                            $('#order_katsub_id').html(response);
                        }
                    });
                } else {
                    $('#order_katsub_id').html('<option value="">-- Pilih Sub Jenis Order --</option>');
                }
            });
            </script>


          <div class="form-group">
            <label class="col-form-label">Shipper :</label>
            <select style="width: 100%;" id="idshipper" class="form-control" name="shipper_id" >
                <option value="0">-- Pilih Shipper --</option>
                <?php
                $sql = $conn->prepare("SELECT * FROM m_user WHERE level_id = 4 ORDER BY id DESC");
                $sql->execute();
                while($data=$sql->fetch()) {
                ?>  
                <option value="<?php echo $data['id'];?>"><?php echo $data['nama'];?></option>
                <?php } ?> 
            </select>
          </div>
          
          
          <div class="form-group">
            <label class="col-form-label">Consignee :</label>
            <select style="width: 100%;" id="idconsignee" class="form-control" name="consignee_id" >
                <option value="0">-- Pilih Consignee --</option>
                <?php
                $sql = $conn->prepare("SELECT * FROM m_user WHERE level_id = 3 ORDER BY id DESC");
                $sql->execute();
                while($data=$sql->fetch()) {
                ?>  
                <option value="<?php echo $data['id'];?>"><?php echo $data['nama'];?></option>
                <?php } ?> 
            </select>
          </div>
          
          
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button  type="submit" name="upload" type="button" class="btn btn-primary" >Save changes</button>
      </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit -->
</div>
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Edit </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form id="form-edit-transaksi-masuk">

              <div class="modal-body">
                <div class="form-group">
                    <input type="hidden" id="id_edit" name="id" />
                    
                    <div class="form-group">
                    <label class="control-label" >Bukti Nota : </label>         
        				<input type="text" class="form-control" name="notes" id="notes_edit" value="" />
        				<small>* nota setelah semua order fix dan dikirim</small><br>
        				<small style="color:red;">* isi dari link youtube</small>
                  </div>
                  
                    <div class="form-group">
                    <label class="control-label" >Nota Sortir : </label>        
        				<input type="text" class="form-control" name="notes_sortir" id="notes_sortir_edit" value="" />
        				<small>* nota saat setelah sortir</small><br>
        				<small style="color:red;">* isi dari link youtube</small>
                  </div>
                    
    			  <div class="form-group">
                    <label class="control-label" >Status : </label>        
    					<input type="text" class="form-control" id="status_edit" name="status" />
    					<small>0. Open</small><br>
    					<small>1. Kirim</small><br>
    					<small>2. Transit</small><br>
    					<small>3. Sampai</small><br>
    					<small>4. Selesai</small><br>
                  </div>
                  
              </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn-save-update">Save changes</button>
              </div>
          </form>
        </div>
      </div>
    </div>
    
    
    
    <!-- Tambah Bayar Edit -->
</div>
<div class="modal fade" id="modaltambahb" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Pembayaran </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form action="tambahb.php" method="post"  enctype="multipart/form-data">

              <div class="modal-body">
                <div class="form-group">
                    <!--<input type="hidden" id="id_edit" name="id" />-->
                    
    			  <!--<div class="form-group">-->
         <!--           <label class="control-label" >Order ID : </label>        -->
    					<!--<input type="text" class="form-control" id="order_id_edit" name="order_id" />-->
         <!--         </div>-->
                  
         <!--         <div class="form-group">-->
         <!--           <label class="control-label" >Shipper ID : </label>         -->
    					<!--<input type="text" class="form-control" id="shipper_id_edit" name="shipper_id" />-->
         <!--         </div>-->
                  
         <!--         <div class="form-group">-->
         <!--           <label class="control-label" >Consignee ID : </label>         -->
    					<!--<input type="text" class="form-control" id="consignee_id_edit" name="consignee_id" />-->
         <!--         </div>-->
                  
                   <input type="hidden" class="form-control" id="order_id_edit" name="order_id" />
                  	<input type="hidden" class="form-control" id="shipper_id_edit" name="shipper_id" />
                  	<input type="hidden" class="form-control" id="consignee_id_edit" name="consignee_id" />
                  	
                  	
                  <div class="form-group">
                    <label class="control-label" >Deskripsi Pembayaran : </label>         
    					<input type="text" class="form-control" name="nama" />
                  </div>
                  
                  <div class="form-group">
                    <label class="control-label" >Tanggal : </label>         
    					<input type="text" class="form-control" name="created_at" value="<?php echo date("Y-m-d H:i:s");?>" />
                  </div>
                  
                  <div class="form-group">
                    <label class="col-form-label">Kategori Kas :</label>
                    <select style="width: 100%;" class="form-control" name="kat_kas_id" >
                        <?php
                        $sql = $conn->prepare("SELECT * FROM m_kategori_kas ORDER BY id DESC");
                        $sql->execute();
                        while($data=$sql->fetch()) {
                        ?>  
                        <option value="<?php echo $data['id'];?>"><?php echo $data['nama'];?></option>
                        <?php } ?> 
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label class="control-label" >Rupiah : <label id="formattedLabel" style="margin-top: 5px; display: block; color: black;"></label> </label>         
    					<input type="text" id="nilaiInput" class="form-control" name="nilai" value="0" />
    					 
                  </div>
                  
                  <div class="form-group">
                    <label class="control-label" >Stat : </label>         
    					<input type="text" class="form-control" name="stat" value="2" />
    					<small style="color:red;">1.Masuk 2. Keluar 3. Deposit </small>
                  </div>
                  
                 
              </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button  type="submit" type="button" class="btn btn-primary" >Save changes</button>
              </div>
          </form>
        </div>
      </div>
    </div>
    
    
    
    
    
    <!-- Modal Form -->
</div>
<div class="modal fade" id="modalform" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Form </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form id="form-edit">

              <div class="modal-body">
                  
                  <div class="form-group">
                    <input type="hidden" id="id_edit" name="id" />
                  
                  <div class="form-group">
                      <label class="control-label">Nomor BASTB :</label>
                      <input type="text" class="form-control" name="no_bastb" id="no_bastb_edit" value="" placeholder="Silahkan isi Nomor BASTB" />
                    </div>
                    
                    <div class="form-group">
                      <label class="control-label">Container / Seal :</label>
                      <input type="text" class="form-control" name="container_seal" id="container_seal_edit" value="" placeholder="Silahkan isi Container / Seal" />
                    </div>
                    
                    <div class="form-group">
                      <label class="control-label">Container Size :</label>
                      <input type="text" class="form-control" name="container_size" id="container_size_edit" value="" placeholder="Silahkan isi Container Size" />
                    </div>
                    
                    <div class="form-group">
                      <label class="control-label">Loading / Destination :</label>
                      <input type="text" class="form-control" name="loading_des" id="loading_des_edit" value="" placeholder="Silahkan isi Loading / Destination" />
                    </div>
                    
                    <div class="form-group">
                      <label class="control-label">Commodity :</label>
                      <input type="text" class="form-control" name="commodity" id="commodity_edit" value="" placeholder="Silahkan isi Commodity" />
                    </div>
                    
                    <div class="form-group">
                      <label class="control-label">Vessel :</label>
                      <input type="text" class="form-control" name="vessel" id="vessel_edit" value="" placeholder="Silahkan isi Vessel" />
                    </div>
                    
                    <div class="form-group">
                      <label class="control-label">Voyage :</label>
                      <input type="text" class="form-control" name="voyage" id="voyage_edit" value="" placeholder="Silahkan isi Voyage" />
                    </div>
                    
                    <div class="form-group">
                      <label class="control-label">Condition :</label>
                      <input type="text" class="form-control" name="conditi" id="conditi_edit" value="" placeholder="Silahkan isi Condition" />
                    </div>
                    
                    <div class="form-group">
                      <label class="control-label">Discharging date :</label>
                      <input type="text" class="form-control" name="discharging_date" id="discharging_date_edit" value="" placeholder="Silahkan isi Discharging date" />
                    </div>

                    
                    
    			  <div class="form-group">
                    <label class="control-label" >Status : </label>        
    					<input type="text" class="form-control" id="status_edit" name="status" />
    					<small>0. Open</small><br>
    					<small>1. Kirim</small><br>
    					<small>2. Transit</small><br>
    					<small>3. Sampai</small><br>
    					<small>4. Selesai</small><br>
                  </div>
                  
              </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn-save-form">Save changes</button>
              </div>
          </form>
        </div>
      </div>
    </div>
    
    
<script>
    const input = document.getElementById("nilaiInput");
    const label = document.getElementById("formattedLabel");

    input.addEventListener("input", function () {
        // Ambil hanya angka
        let rawValue = this.value.replace(/\D/g, '');

        if (rawValue === "") {
            label.textContent = "";
            return;
        }

        // Ubah ke format Indonesia tanpa desimal
        let formatted = parseInt(rawValue).toLocaleString("id-ID");

        label.textContent = formatted;
    });
</script>



   
  
  <?php
  include '../footer.php';
  ?>

<script type="text/javascript">
     $(document).ready(function(){
         
        $('#tambah').modal('show');
        
        // $('#btn-save-update').click(function(){
        //   $.ajax({
        //       url: "edit.php",
        //       type : 'post',
        //       data : $('#form-edit-transaksi-masuk').serialize(),
        //       success: function(data){
        //           var res = JSON.parse(data);
        //           if (res.code == 200){
        //               alert('Success Update');
        //               location.reload();
        //           }
        //       }
        //   }) 
        // });
        
        $('#btn-save-form').click(function(){
           $.ajax({
               url: "editform.php",
               type : 'post',
               data : $('#form-edit').serialize(),
               success: function(data){
                   var res = JSON.parse(data);
                   if (res.code == 200){
                       alert('Success Update');
                       location.reload();
                   }
               }
           }) 
        });
        
        
        // $('#btn-save-tambahb').click(function(){
        //   $.ajax({
        //       url: "tambahb.php",
        //       type : 'post',
        //       data : $('#form-tambahb').serialize(),
        //       success: function(data){
        //           var res = JSON.parse(data);
        //           if (res.code == 200){
        //               alert('Success Update');
        //               location.reload();
        //           }
        //       }
        //   }) 
        // });
        
        $(document).on('click','.btn_update',function(){
            console.log("Masuk");
            $("#id_edit").val($(this).attr('data-id'));
            $("#notes_edit").val($(this).attr('data-notes'));
            $("#notes_sortir_edit").val($(this).attr('data-notes_sortir'));
            $("#status_edit").val($(this).attr('data-status'));
            $('#modalEdit').modal('show');
        });
        
        $(document).on('click','.btn_tambahb',function(){
            console.log("Masuk");
            $("#id_edit").val($(this).attr('data-id'));
            $("#order_id_edit").val($(this).attr('data-order_id'));
            $("#shipper_id_edit").val($(this).attr('data-shipper_id'));
            $("#consignee_id_edit").val($(this).attr('data-consignee_id'));
            $('#modaltambahb').modal('show');
        });
        
        $(document).on('click','.btn_form',function(){
            console.log("Masuk");
            $("#id_edit").val($(this).attr('data-id'));
            // $("#order_id_edit").val($(this).attr('data-order_id'));
            // $("#shipper_id_edit").val($(this).attr('data-shipper_id'));
            // $("#consignee_id_edit").val($(this).attr('data-consignee_id'));
            $('#modalform').modal('show');
        });
    });
  </script>   
  
  
    <script>
 $(function(){
  $('#idshipper').select2({
    dropdownParent: $('#tambah')
  });
  $('#idconsignee').select2({
    dropdownParent: $('#tambah')
  });
}); 
</script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
  $(function () {
    $('#example1').DataTable({
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'scrollX'     : true,
      'autoWidth'   : false
    })
  })
</script>


</body>
</html>
