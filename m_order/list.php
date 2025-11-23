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

$codx = $_GET['codx'];
$master = "List";
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
        <a type="button" href="index.php" class="btn btn-primary" >
            Kembali
              </a>
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
                  <th>Status</th>
                  <th>Tanggal</th>
                  <th>Deskripsi</th>
                  <th>Nilai</th>
                  <th>Aksi</th>
                </tr>
                </thead>
                <tbody>

                <?php
                   $count = 1;
                    
                    $sql = $conn->prepare("SELECT * FROM `m_kas` WHERE order_codx = :order_codx ORDER BY id DESC");
                    $sql->bindParam(':order_codx', $codx, PDO::PARAM_STR); // asumsi $ocodx adalah string
                    $sql->execute();
				   
                   //$sql = $conn->prepare("SELECT * FROM `m_kas` WHERE order_codx = $ocodx ORDER BY id DESC");
                   
                   $sql->execute();
                   while($data=$sql->fetch()) {
                       
                       if ($data['stat'] == 1) {
                            $total_pemasukan += $data['nilai'];
                        } elseif ($data['stat'] == 4) {
                            $total_piutang += $data['nilai'];
                        }
                ?>

                <tr>
                  <td><?php echo $count; ?></td>
                  <td><?php
                    if ($data['stat'] == 1) {
                        echo 'Pemasukan';
                    }elseif ($data['stat'] == 2) {
                        echo 'Pengeluaran';
                    } elseif ($data['stat'] == 3) {
                        echo 'Deposit';
                    } elseif ($data['stat'] == 4) {
                        echo 'Piutang';
                    } else {
                        echo 'Status tidak dikenal';
                    }
                    ?>
                    </td>
                  <td><?php echo date('d-m-Y H:i', strtotime($data['created_at']));?></td>
                  <td><?php echo $data['nama'];?></td>
                  <td><?php echo "Rp " . number_format($data['nilai'], 0, ',', '.');?></td> 
                  
                  <td>
                      <?php if ($data['stat'] == 4) { ?>
                      <small><a class="btn btn-info" href="cetak_satuan.php?codx=<?php echo $data['codx']; ?>&order_codx=<?php echo $codx; ?>">Cetak</a> </small>
                      <?php } ?>
                      <button 
                      data-id="<?= $data['id'] ?>" 
                      data-stat="<?= $data['stat'] ?>"
                      data-created_at="<?= $data['created_at'] ?>" 
                      data-nama="<?= $data['nama'] ?>"
                      data-nilai="<?= $data['nilai']?>"
                      type="button" class="btn btn-light btn_update" data-toggle="modal">
                          ✎
                      </button>
                      
                      
                    <a class="btn btn-light" onclick="return confirm('are you want deleting data')" href="hapus.php?op=hapus&id=<?php echo $data['id']; ?>&oid=<?php echo $oid; ?>">❌</a>
                  </td>
                </tr>

                <?php
                
                $count=$count+1;
                } 
                ?>
                Total Bayar = <?= "Rp. " . number_format($total_piutang, 0, ',', '.') . ",-" ?><br>
                Pembayaran = <?= "Rp. " . number_format($total_pemasukan, 0, ',', '.') . ",-" ?><br>
                
                <?php 
                $sisa = $total_piutang - $total_pemasukan;
                ?>
                <b>Sisa = <?= "Rp. " . number_format($sisa, 0, ',', '.') . ",-" ?></b><br>

                </tbody>
                <tfoot>
                <tr>
                  <th>No</th>
                  <th>Status</th>
                  <th>Tanggal</th>
                  <th>Deskripsi</th>
                  <th>Nilai</th>
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
                    <label class="control-label" >Status : </label>        
    					<input type="text" class="form-control" id="stat_edit" name="stat" />
    					<small style="color:red;">1.Masuk 2. Keluar 3. Deposit </small>
                  </div>
                  
                  <div class="form-group">
                    <label class="control-label" >Tanggal : </label>        
    					<input type="text" class="form-control" id="created_at_edit" name="created_at" />
    					<small>* tahun-bulan-tgl waktu</small>
                  </div>
                    
    			  <div class="form-group">
                    <label class="control-label" >Deskripsi : </label>        
    					<input type="text" class="form-control" id="nama_edit" name="nama" />
                  </div>
                  
                  <div class="form-group">
                    <label class="control-label" >Nilai : <label id="formattedLabel" style="margin-top: 5px; display: block; color: black;"></label></label>        
    					<input type="text" class="form-control" id="nilai_edit" name="nilai" />
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
    
  
  <?php
  include '../footer.php';
  ?>

<script type="text/javascript">
     $(document).ready(function(){
        
        $('#btn-save-update').click(function(){
           $.ajax({
               url: "edit_list.php",
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
        
        
     
        
        $(document).on('click','.btn_update',function(){
            console.log("Masuk");
            $("#id_edit").val($(this).attr('data-id'));
            $("#stat_edit").val($(this).attr('data-stat'));
            $("#created_at_edit").val($(this).attr('data-created_at'));
            $("#nama_edit").val($(this).attr('data-nama'));
            $("#nilai_edit").val($(this).attr('data-nilai'));
            
            
            // Panggil fungsi format setelah nilai di-set
    updateFormattedLabel();
    
    
            $('#modalEdit').modal('show');
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

<script>
const input = document.getElementById("nilai_edit");
const label = document.getElementById("formattedLabel");

function updateFormattedLabel() {
    let rawValue = input.value.replace(/\D/g, '');

    if (rawValue === "") {
        label.textContent = "";
        return;
    }

    let formatted = parseInt(rawValue).toLocaleString("id-ID");
    label.textContent = formatted;
}

input.addEventListener("input", updateFormattedLabel);

// Trigger ketika modal dibuka
$('#modalEdit').on('shown.bs.modal', function () {
    updateFormattedLabel();
});
</script>


</body>
</html>
