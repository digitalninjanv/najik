<?php
include '../../config.php';
error_reporting(0);

/* Halaman ini tidak dapat diakses jika belum ada yang login(masuk) */
if(isset($_SESSION['email'])== 0) {
	header('Location: ../../index.php');
}

if( $_SESSION['level_id'] == "1" || $_SESSION['level_id'] == "3" ){
}else{
  echo "<script>alert('Maaf! anda tidak bisa mengakses halaman ini '); document.location.href='../admin/'</script>";
}

$master = "Deposit";
$dba = "order";
$ket = "| Nomor Hp - Alamat";
$ketnama = "Silahkan mengisi nama";

?>
 
  <?php
  include '../header.php';
  include '../sidebar.php';
  ?>



  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>

        Master Data <?php echo $master; ?>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Tables <?php echo $master; ?></a></li>
        <li class="active">Master Data <?php echo $master; ?></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <!-- /.box -->


          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Master Data <?php echo $master; ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>No</th>
                  <th>Created at</th>
                  <th>Order</th>
                  <th>Source</th>
                  <th>Supplier</th>
                  <th>Deposit</th>
                  <th>Pengeluaran</th>
                  <th>Sisa</th>
                  <th>Aksi</th>
                </tr>
                </thead>
                <tbody>

                <?php
                   $count = 1;
				   
                   $sql = $conn->prepare("SELECT * FROM `m_order` WHERE status IN (0, 1, 2, 3) ORDER BY id DESC");
                   $sql->execute();
                   while($data=$sql->fetch()) {
                ?>

                <tr>
                  <td><?php echo $count; ?></td>
                  <td><?php echo date('d-m-Y H:i:s', strtotime($data['created_at'])); ?></td>
                  
                  <?php
                    
                    // Mapping status berdasarkan nilai
                    $statusList = [
                        0 => 'open',
                        1 => 'terkirim',
                        2 => 'transit',
                        3 => 'sampai',
                        4 => 'selesai'
                    ];
                    
                    // Ambil status sesuai nilai, fallback ke 'tidak diketahui' kalau nggak cocok
                    $status = isset($statusList[$data['status']]) ? $statusList[$data['status']] : 'tidak diketahui';
                    ?>
                    
                    
                  <td><?php echo $data['nama'];?> 
                  
                  <small 
                      data-id="<?= $data['id'] ?>" 
                      data-notes="<?= $data['notes'] ?>" 
                      data-status="<?= $data['status'] ?>" 
                      type="button" data-toggle="modal"
                  class="label pull-right bg-blue btn_update" style="cursor: pointer;"><?= $status ?></small>
                  
                  <!--<button -->
                  <!--    data-id="<?= $data['id'] ?>" -->
                  <!--    data-status="<?= $data['status'] ?>" -->
                  <!--    type="button" class="btn btn-light btn_update" data-toggle="modal">✎</button>-->
                  
                  
                  </td>
                  <?php
                  $stmt = $conn->prepare("SELECT * FROM m_user WHERE id = '".$data['source_id']."'");
                  $stmt->execute();
                  $row = $stmt->fetch();
                  ?>
                  <td><?php echo $row['nama'];?></td>
                  <?php
                  $stmt = $conn->prepare("SELECT * FROM m_user WHERE id = '".$data['supplier_id']."'");
                  $stmt->execute();
                  $row = $stmt->fetch();
                  ?>
                  <td><?php echo $row['nama'];?></td>
                  <?php
                  $stmt = $conn->prepare("SELECT SUM(nilai) AS depo_nilai 
                        FROM m_kas 
                        WHERE stat = 3 AND order_id = :order_id");
                        $stmt->bindParam(':order_id', $data['id']);
                        $stmt->execute();
                        $row = $stmt->fetch();
                  ?>
                  
                  <td><?php echo "Rp " . number_format($row['depo_nilai'], 0, ',', '.');?></td>
                  
                  <?php $deponilai = $row['depo_nilai']; ?>
                  <?php
                  $stmt = $conn->prepare("SELECT SUM(nilai) AS total_nilai 
                        FROM m_kas 
                        WHERE stat = 2 AND order_id = :order_id");
                        $stmt->bindParam(':order_id', $data['id']);
                        $stmt->execute();
                        $row = $stmt->fetch();
                  ?>
                  <td><?php echo "Rp " . number_format($row['total_nilai'], 0, ',', '.');?></td>
                  <?php $totalnilai = $row['total_nilai']; ?>
                  <?php $sisa = $deponilai - $totalnilai?>
                  <td><?php echo "Rp " . number_format($sisa, 0, ',', '.');?></td> 
                  <td>
                      <a href="list.php?oid=<?php echo $data['id'];?>">
                          👁️
                      </a>
                      
                      
                        <?php if (!empty($data['notes'])): ?>
                            <a href="<?php echo $data['notes']; ?>">
                                📝
                            </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($data['notes_sortir'])): ?>
                            <a href="<?php echo $data['notes_sortir']; ?>">
                                📓
                            </a>
                        <?php endif; ?>
                      
                      
                      
                      <button  
                      data-order_id="<?= $data['id'] ?>" 
                      data-source_id="<?= $data['source_id'] ?>" 
                      data-supplier_id="<?= $data['supplier_id'] ?>" 
                      type="button" class="btn btn-light btn_tambahb" data-toggle="modal">
                      ➕
                      </button>
                      
                      
                    <a class="btn btn-light" onclick="return confirm('are you want deleting data')" href="../../controller/<?php echo $dba;?>_controller.php?op=hapus&id=<?php echo $data['id']; ?>">❌</a>
                  
                  
                  </td>
                </tr>

                <?php
                $deponilais += $deponilai;
                
                
                
                
                $count=$count+1;
                } 
                ?>
                
                <b>Total Deposit = <?= "Rp. ".number_format($deponilais,0).",-" ?> </b><br>

                </tbody>
                <tfoot>
                <tr>
                  <th>No</th>
                  <th>Created at</th>
                  <th>Order</th>
                  <th>Source</th>
                  <th>Supplier</th>
                  <th>Deposit</th>
                  <th>Pengeluaran</th>
                  <th>Sisa</th>
                  <th>Aksi</th>
                </tr>
                </tfoot>
              </table>
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
        <h5 class="modal-title" id="exampleModalLabel">Tambah <?php echo $master;?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form action="../../controller/order_controller.php?op=tambah" method="post"  enctype="multipart/form-data">
              
          <div class="form-group">
            <label class="control-label" >Tanggal : </label>         
				<input type="text" class="form-control" name="created_at" value="<?php echo date("Y-m-d H:i:s");?>" />
				<small style="color:red;">tahun-bulan-tgl jam</small>
          </div>

          <div class="form-group">
            <label class="col-form-label">Sourching :</label>
            <select style="width: 100%;" class="form-control" name="source_id" >
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
            <label class="col-form-label">Supplier :</label>
            <select style="width: 100%;" class="form-control" name="supplier_id" >
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
         <!--           <label class="control-label" >Source ID : </label>         -->
    					<!--<input type="text" class="form-control" id="source_id_edit" name="source_id" />-->
         <!--         </div>-->
                  
         <!--         <div class="form-group">-->
         <!--           <label class="control-label" >Supplier ID : </label>         -->
    					<!--<input type="text" class="form-control" id="supplier_id_edit" name="supplier_id" />-->
         <!--         </div>-->
                  
                   <input type="hidden" class="form-control" id="order_id_edit" name="order_id" />
                  	<input type="hidden" class="form-control" id="source_id_edit" name="source_id" />
                  	<input type="hidden" class="form-control" id="supplier_id_edit" name="supplier_id" />
                  	
                  	
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
    					<input type="text" class="form-control" name="stat" value="3" />
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
        
        $('#btn-save-update').click(function(){
           $.ajax({
               url: "edit.php",
               type : 'post',
               data : $('#form-edit-transaksi-masuk').serialize(),
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
            $("#status_edit").val($(this).attr('data-status'));
            $('#modalEdit').modal('show');
        });
        
        $(document).on('click','.btn_tambahb',function(){
            console.log("Masuk");
            $("#id_edit").val($(this).attr('data-id'));
            $("#order_id_edit").val($(this).attr('data-order_id'));
            $("#source_id_edit").val($(this).attr('data-source_id'));
            $("#supplier_id_edit").val($(this).attr('data-supplier_id'));
            $('#modaltambahb').modal('show');
        });
    });
  </script>   

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
