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

$master = "Order";
$dba = "order";
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
        <!--<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tambah">-->
        <!--    Tambah-->
        <!--      </button>-->
              
              
            <!--  <button type="button" class="btn btn-success" data-toggle="modal" data-target="#tambah">-->
            <!--Tambah-->
            <!--  </button>-->
             <div class="box">
            <form method="GET" action="">
              <label>Dari Tanggal:</label>
              <input type="date" name="tgl_mulai" value="<?= isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : '' ?>">
              
              <label>Sampai Tanggal:</label>
              <input type="date" name="tgl_selesai" value="<?= isset($_GET['tgl_selesai']) ? $_GET['tgl_selesai'] : '' ?>">
              
              <button type="submit" class="btn btn-success">Filter</button>
              <a href="periode.php" class="btn btn-danger">Reset</a>
            </form>
            <br>
            </div>
              
              <a class="btn btn-primary" href="tambah.php">Tambah</a>
          <!-- /.box -->


          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Master Data <?php echo $master; ?></h3>
              <i><p>Diurutkan berdasarkan order open pertama yang perlu di tindak lanjuti</p></i>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>No</th>
                  <th>Order</th>
                  <th>Shipper</th>
                  <th>Consignee</th>
                  <th>Belum Bayar
                  <small><span style="cursor: pointer;"
                  title="Ini adalah total piutang yang harus di bayar oleh customer atau shipper.">❗</span>
                  </small>
                  </th>
                  <th>Pembayaran<small><span style="cursor: pointer;"
                  title="Ini adalah pembayaran yang sudah customer atau shipper bayar akan muncul disini">❗</span>
                  </small>
                  </th>
                  <th>Sisa
                  <small><span style="cursor: pointer;"
                  title="Ini adalah sisa dari hasil total piutang dikurang pembayaran">❗</span>
                  </small>
                  </th>
                  <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                    
                    <?php
                    $count = 1;
                    
                    if (!empty($_GET['tgl_mulai']) && !empty($_GET['tgl_selesai'])) {
                        $sql = $conn->prepare("SELECT * FROM `m_order` 
                                               WHERE DATE(created_at) BETWEEN :tgl_mulai AND :tgl_selesai
                                               ORDER BY id DESC");
                        $sql->execute([
                            ':tgl_mulai' => $_GET['tgl_mulai'],
                            ':tgl_selesai' => $_GET['tgl_selesai']
                        ]);
                    } else {
                        // default tampil semua data
                        $sql = $conn->prepare("SELECT * FROM `m_order` ORDER BY id DESC");
                        $sql->execute();
                    }
                   while($data=$sql->fetch()) {
                ?>

                <tr>
                  <td><?php echo $count; ?></td>
                  
                  <?php
                    
                    // Mapping status berdasarkan nilai
                    $statusList = [
                        0 => 'open',
                        1 => 'Termuat Container',
                        2 => 'Stack Depo Asal',
                        3 => 'Onboard',
                        4 => 'Ongoing',
                        5 => 'Arrive',
                        6 => 'Stack Depo Tujuan',
                        7 => 'Dooring'
                    ];
                    
                    // Ambil status sesuai nilai, fallback ke 'tidak diketahui' kalau nggak cocok
                    $status = isset($statusList[$data['status']]) ? $statusList[$data['status']] : 'tidak diketahui';
                    ?>
                    
                    
                  <td>
                      <small style="background-color:yellow;">
                       <?php
                            $stmt = $conn->prepare("SELECT * FROM m_order_kat WHERE id = :order_kat_id");
                            $stmt->execute([':order_kat_id' => $data['order_kat_id']]);
                            $row = $stmt->fetch();
                            echo $row['nama'];
                        ?>
                      - 
                       <?php
                            $stmt = $conn->prepare("SELECT * FROM m_order_katsub WHERE id = :order_katsub_id");
                            $stmt->execute([':order_katsub_id' => $data['order_katsub_id']]);
                            $row = $stmt->fetch();
                            echo $row['nama'];
                        ?>
                        </small>
                      
                      
                      
                      <small><?php echo date('d-m-Y H:i:s', strtotime($data['created_at'])); ?></small> - <?php echo $data['nama'];?> 
                  
                  <small
                  data-id="<?= $data['id'] ?>" 
                      data-no_bastb="<?= $data['no_bastb'] ?>"
                        data-container_seal="<?= $data['container_seal'] ?>"
                        data-container_size="<?= $data['container_size'] ?>"
                        data-loading_des="<?= $data['loading_des'] ?>"
                        data-commodity="<?= $data['commodity'] ?>"
                        data-vessel="<?= $data['vessel'] ?>"
                        data-voyage="<?= $data['voyage'] ?>"
                        data-conditi="<?= $data['conditi'] ?>"
                        data-discharging_date="<?= $data['discharging_date'] ?>"
                      data-status="<?= $data['status'] ?>" 
                      type="button" data-toggle="modal"
                  class="label pull-right bg-green btn_update" style="cursor: pointer;">Form</small>
                  
                  
                  <small
                      data-idi="<?= $data['id'] ?>"
                      data-shipper_id="<?= $data['shipper_id'] ?>"
                      data-invoice_no="<?= $data['invoice_no'] ?>"
                        data-invoice_date="<?= $data['invoice_date'] ?>"
                        data-credit_terms="<?= $data['credit_terms'] ?>"
                        data-attn="<?= $data['attn'] ?>"
                        data-address="<?= $data['address'] ?>"
                        data-total_amount="<?= $data['total_amount'] ?>"
                        data-muncul_rek="<?= $data['muncul_rek'] ?>"
                        data-reke="<?= $data['rek_id'] ?>"
                      type="button" data-toggle="modal"
                  class="label pull-right bg-yellow btn_invoice" style="cursor: pointer;">Invoice</small>

                  <small 
                  class="label pull-right bg-blue" style="cursor: pointer;"><?= $status ?></small>
                  
                  <!--<button -->
                  <!--    data-id="<?= $data['id'] ?>" -->
                  <!--    data-status="<?= $data['status'] ?>" -->
                  <!--    type="button" class="btn btn-light btn_update" data-toggle="modal">✎</button>-->
                  
                  
                  </td>
                  <td>
                        <?php
                        if ($data['shipper_id'] == '0') {
                            echo "<small>-- Belum --</small>";
                        } else {
                            $stmt = $conn->prepare("SELECT nama FROM m_user WHERE id = :id");
                            $stmt->execute([':id' => $data['shipper_id']]);
                            $row = $stmt->fetch();
                            echo htmlspecialchars($row['nama']);
                        }
                        ?>
                        <!--<small -->
                        <!--    data-ids="<?= $data['id'] ?>" -->
                        <!--    type="button" -->
                        <!--    data-toggle="modal"-->
                        <!--    class="label pull-right bg-yellow btn_shipper" -->
                        <!--    style="cursor: pointer;">-->
                        <!--    Ubah-->
                        <!--</small>-->
                        
                        
                        <!--<small type="button" -->
                        <!--        class="btn btn-primary" -->
                        <!--        data-toggle="modal" -->
                        <!--        data-target="#shipper"-->
                        <!--        data-id="<?= $data['id'] ?>">-->
                        <!--    Tambah-->
                        <!--</small>-->
                        
                        <a
                                style="cursor: pointer;"
                                data-toggle="modal" 
                                data-target="#shipper"
                                data-id="<?= $data['id'] ?>">
                            Ubah
                        </a>
                    </td>
                    
                    
                    <td>
                        <?php
                        if ($data['consignee_id'] == '0') {
                            echo "<small>-- Belum --</small>";
                        } else {
                            $stmt = $conn->prepare("SELECT nama FROM m_user WHERE id = :id");
                            $stmt->execute([':id' => $data['consignee_id']]);
                            $row = $stmt->fetch();
                            echo htmlspecialchars($row['nama']);
                        }
                        ?>
                        
                        <a
                                style="cursor: pointer;"
                                data-toggle="modal" 
                                data-target="#consignee"
                                data-id="<?= $data['id'] ?>">
                            Ubah
                        </a>
                    </td>
                  
                  <?php
                  $stmt = $conn->prepare("SELECT SUM(nilai) AS total_nilai 
                        FROM m_kas 
                        WHERE stat = 4 AND order_id = :order_id");
                        $stmt->bindParam(':order_id', $data['id']);
                        $stmt->execute();
                        $row = $stmt->fetch();
                  ?>
                  
                  <td><small><?php echo "Rp " . number_format($row['total_nilai'], 0, ',', '.');?></small></td>
                  
                  <?php $totalnilai = $row['total_nilai']; ?>
                  <?php
                  $stmt = $conn->prepare("SELECT SUM(nilai) AS total_bayar 
                        FROM m_kas 
                        WHERE stat = 1 AND order_id = :order_id");
                        $stmt->bindParam(':order_id', $data['id']);
                        $stmt->execute();
                        $row = $stmt->fetch();
                  ?>
                  <td><small><?php echo "Rp " . number_format($row['total_bayar'], 0, ',', '.');?></small></td>
                  <?php $totalbayar = $row['total_bayar']; ?>
                  <?php $sisa = $totalnilai - $totalbayar?>
                  <td><small><?php echo "Rp " . number_format($sisa, 0, ',', '.');?></small></td> 
                  <td>
                      <small> <a href="invoice.php?codx=<?php echo $data['codx']; ?>">Invoice</a> | <a href="full.php?codx=<?php echo $data['codx']; ?>">Full</a> | <a href="suratjalan.php">Surat Jalan</a> | <a href="tandaterima.php">Tanda Terima</a> </small>
                      
                      
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
                      data-shipper_id="<?= $data['shipper_id'] ?>" 
                      data-consignee_id="<?= $data['consignee_id'] ?>" 
                      type="button" class="btn btn-light btn_tambahb" data-toggle="modal">
                      ➕
                      </button>
                    <a class="btn btn-light" onclick="return confirm('are you want deleting data')" href="../../controller/<?php echo $dba;?>_controller.php?op=hapus&id=<?php echo $data['id']; ?>">❌</a>
                  </td>
                </tr>

                <?php
                
                $totalnilais += $totalnilai;
                $totalbayars += $totalbayar;
                
                
                $sisa = $totalnilais - $totalbayars;
                
                
                $count=$count+1;
                } 
                ?>
                
                <b>Belum Bayar = <?= "Rp. ".number_format($totalnilais,0).",-" ?> </b><br>
                Sudah Bayar = <?= "Rp. ".number_format($totalbayars,0).",-" ?><br>
                Sisa = <?= "Rp. ".number_format($sisa,0).",-" ?><br>
                
                </tbody>
                <tfoot>
                <tr>
                  <th>No</th>
                  <th>Order</th>
                  <th>Shipper</th>
                  <th>Consignee</th>
                  <th>Total Biaya</th>
                  <th>Bayar</th>
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
            <label class="col-form-label">Shipper :</label>
            <select style="width: 100%;" class="form-control" name="shipper_id" >
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
            <select style="width: 100%;" class="form-control" name="consignee_id" >
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


 <!-- Modal Shipper -->
<div class="modal fade" id="shipper" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Ubah</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form action="../../controller/order_controller.php?op=shipper" method="post"  enctype="multipart/form-data">
              
          <input type="hidden" id="id_coba" name="id" />
          
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
          
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button  type="submit" type="button" class="btn btn-primary" >Save changes</button>
      </div>
      </form>
    </div>
  </div>
</div>


 <!-- Modal Consignee -->
<div class="modal fade" id="consignee" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Ubah</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form action="../../controller/order_controller.php?op=consignee" method="post"  enctype="multipart/form-data">
              
          <input type="hidden" id="id_coba" name="id" />
          
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
        <button  type="submit" type="button" class="btn btn-primary" >Save changes</button>
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
                      
                      <small style="color:red">* isi sesuai nomor yang ada dibawah </small>
                      <table style="border: 1px solid black; border-collapse: collapse; width: 100%; color: black;" id="example1" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                          <th>No</th>
                          <th>Nama</th>
                          <th>Des</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                           $count = 1;
        				   
                           $sql = $conn->prepare("SELECT * FROM `m_condition`");
                           $sql->execute();
                           while($data=$sql->fetch()) {
                        ?>
                          <tr>
                          <td><?php echo $count; ?></td>
                          <td><?php echo $data['nama'];?></td>
                          <td><?php echo $data['des'];?></td>
                          </tr>
                        <?php 
                        $count=$count+1;
                        } ?>
                        
                         </tbody>
                      </table>
                
                
                
                    </div>
                    
                    <div class="form-group">
                      <label class="control-label">Discharging date :</label>
                      <input type="text" class="form-control" name="discharging_date" id="discharging_date_edit" value="" placeholder="Silahkan isi Discharging date" />
                    </div>

                    
                    
    			  <div class="form-group">
                    <label class="control-label" >Status : </label>        
    					<input type="text" class="form-control" id="status_edit" name="status" />
    					<small>0. Open</small><br>
    					<small>1. Termuat Container</small><br>
    					<small>2. Stack Depo Asal</small><br>
    					<small>3. Onboard</small><br>
    					<small>4. Ongoing</small><br>
    					<small>5. Arrive</small><br>
    					<small>6. Stack Depo Tujuan</small><br>
    					<small>7. Dooring</small><br>
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
    
    
    
    
    
    <!-- Modal Invoice -->
</div>
<div class="modal fade" id="modalinvoice" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Edit </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form id="form-invoice">

              <div class="modal-body">
                  
                  <div class="form-group">
                    <input type="hidden" id="idi_edit" name="id" />
                    <input type="hidden" id="shipper_id_edit" name="shipper_id" />
                    
                    <div class="form-group">
                      <label class="control-label">Invoice No :</label>
                      <input type="text" class="form-control" name="invoice_no" id="invoice_no_edit" value="" placeholder="Silahkan isi Invoice No" />
                    </div>
                    
                    <div class="form-group">
                      <label class="control-label">Invoice Date :</label>
                      <input type="text" class="form-control" name="invoice_date" id="invoice_date_edit" value="" placeholder="Silahkan isi Invoice Date" />
                    </div>
                    
                    <div class="form-group">
                      <label class="control-label">Credit Terms :</label>
                      <input type="text" class="form-control" name="credit_terms" id="credit_terms_edit" value="" placeholder="Silahkan isi Credit Terms" />
                    </div>
                    
                    <div class="form-group">
                      <label class="control-label">Attn :</label>
                      <input type="text" class="form-control" name="attn" id="attn_edit" value="" placeholder="Silahkan isi Attn" />
                    </div>
                    
                    <div class="form-group">
                      <label class="control-label">Address :</label>
                      <input type="text" class="form-control" name="address" id="address_edit" value="" placeholder="Silahkan isi Address" />
                    </div>
                    
                    <div class="form-group">
                    <label class="control-label" >Total Amount : <label id="totalm" style="margin-top: 5px; display: block; color: black;"></label> </label>         
    					<input type="text" class="form-control" name="total_amount" id="total_amount_edit" value="0" />
    					 
                     </div>
                     
                     
                     <script>
                        const input = document.getElementById("total_amount_edit");
                        const label = document.getElementById("totalm");
                    
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
                    
                    <div class="form-group">
                      <label class="control-label">Munculkan Rek :</label>
                      <input type="text" class="form-control" name="muncul_rek" id="muncul_rek_edit" value="" placeholder="Silahkan isi Muncul Rek" />
                      <small style="color:red;">0. Tidak 1. Aktif</small>
                    </div>
                    
                    
                    <?php
                    // Ambil data m_rek
                    $stmt = $conn->prepare("SELECT * FROM m_rek");
                    $stmt->execute();
                    $reks = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    
                    <div class="form-group">
                        <label class="control-label">Pilih Rekening :</label>
                        <select id="rek_select" class="form-control">
                            <option value="">-- Pilih Rekening --</option>
                            <?php foreach ($reks as $rek): ?>
                                <option value="<?php echo ($rek['id']); ?>">
                                    <?php echo htmlspecialchars($rek['nama']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <input type="hidden" class="form-control" name="rek_id" id="reke_edit" value="" />
                    </div>
                    
                    <script>
                    // Ketika select option berubah
                    document.getElementById('rek_select').addEventListener('change', function() {
                        document.getElementById('reke_edit').value = this.value;
                    });
                    </script>


                    
                    <!--<div class="form-group">-->
                    <!--  <label class="control-label">Total Amount :</label>-->
                    <!--  <input type="text" class="form-control" name="total_amount" id="total_amount_edit" value="" placeholder="Silahkan isi Total Amount" />-->
                    <!--</div>-->
                  
              </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn-invoice">Save changes</button>
              </div>
          </form>
        </div>
      </div>
    </div>
    
    
   <!-- Modal Ubah Shipper -->
<div class="modal fade" id="modalshipper" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Ganti Shipper</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <div class="modal-body">
          <form action="../../controller/order_controller.php?op=shipper" method="post"  enctype="multipart/form-data">
              
          <input type="text" id="ids_edit" name="id" />
          
          <div class="form-group">
            <label class="col-form-label">Shipper :</label>
            <select style="width: 100%;" class="form-control" name="shipper_id" >
                <?php
                $sql = $conn->prepare("SELECT * FROM m_user WHERE level_id = 4 ORDER BY id DESC");
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
        <!--<button type="button" class="btn btn-primary" id="btn-shipper">Save changes</button>-->
        <button  type="submit" type="button" class="btn btn-primary" >Save changes</button>
      </div>
      </form>
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
                  	<input type="hidden" class="form-control" id="shippert_id_edit" name="shipper_id" />
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
                        <option value="0">-- Pilih --</option>
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
    					<input type="text" class="form-control" name="stat" value="1" />
    					<small style="color:red;">1.Masuk 2. Keluar 3. Deposit 4. Piutang 5. Piutang Lunas </small><br>
    					<small style="color:green;">informasi bahwa hanya akan gunakan status 1 atau 4</small>
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
        
        
        $('#btn-invoice').click(function(){
           $.ajax({
               url: "editi.php",
               type : 'post',
               data : $('#form-invoice').serialize(),
               success: function(data){
                   var res = JSON.parse(data);
                   if (res.code == 200){
                       alert('Success Update');
                       location.reload();
                   }
               }
           }) 
        });
        
        $('#btn-shipper').click(function(){
          $.ajax({
              url: "edit_shipper.php",
              type : 'post',
              data : $('#form-shipper').serialize(),
              success: function(data){
                  var res = JSON.parse(data);
                  if (res.code == 200){
                      alert('Success Update');
                      location.reload();
                  }
              }
          }) 
        });
        
        
        $('#btn-save-tambahb').click(function(){
          $.ajax({
              url: "tambahb.php",
              type : 'post',
              data : $('#form-tambahb').serialize(),
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
            $("#no_bastb_edit").val($(this).attr('data-no_bastb'));
            $("#container_seal_edit").val($(this).attr('data-container_seal'));
            $("#container_size_edit").val($(this).attr('data-container_size'));
            $("#loading_des_edit").val($(this).attr('data-loading_des'));
            $("#commodity_edit").val($(this).attr('data-commodity'));
            $("#vessel_edit").val($(this).attr('data-vessel'));
            $("#voyage_edit").val($(this).attr('data-voyage'));
            $("#conditi_edit").val($(this).attr('data-conditi'));
            $("#discharging_date_edit").val($(this).attr('data-discharging_date'));
            $("#status_edit").val($(this).attr('data-status'));
            $('#modalEdit').modal('show');
        });
        
        $(document).on('click','.btn_invoice',function(){
            console.log("Masuk");
            $("#idi_edit").val($(this).attr('data-idi'));
            $("#shipper_id_edit").val($(this).attr('data-shipper_id'));
            $("#invoice_no_edit").val($(this).attr('data-invoice_no'));
            $("#invoice_date_edit").val($(this).attr('data-invoice_date'));
            $("#credit_terms_edit").val($(this).attr('data-credit_terms'));
            $("#attn_edit").val($(this).attr('data-attn'));
            $("#address_edit").val($(this).attr('data-address'));
            $("#total_amount_edit").val($(this).attr('data-total_amount'));
            $("#muncul_rek_edit").val($(this).attr('data-muncul_rek'));
            $("#reke_edit").val($(this).attr('data-reke'));
            $('#modalinvoice').modal('show');
        });
        
        $(document).on('click','.btn_shipper',function(){
            console.log("Masuk");
            $("#ids_edit").val($(this).attr('data-ids'));
            $('#modalshipper').modal('show');
        });
        
        $(document).on('click','.btn_tambahb',function(){
            console.log("Masuk");
            $("#id_edit").val($(this).attr('data-id'));
            $("#order_id_edit").val($(this).attr('data-order_id'));
            $("#shippert_id_edit").val($(this).attr('data-shipper_id'));
            $("#consignee_id_edit").val($(this).attr('data-consignee_id'));
            $('#modaltambahb').modal('show');
        });
    });
  </script>   
  
     <script>
 $(function(){
  $('#idshipper').select2({
    dropdownParent: $('#shipper')
  });
  $('#idconsignee').select2({
    dropdownParent: $('#consignee')
  });
}); 
</script>


        <script>
$('#shipper').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);  // tombol yang diklik
    var id     = button.data('id');       // ambil nilai dari data-id
    var modal  = $(this);
    modal.find('#id_coba').val(id);       // isi ke input modal
});
$('#consignee').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);  // tombol yang diklik
    var id     = button.data('id');       // ambil nilai dari data-id
    var modal  = $(this);
    modal.find('#id_coba').val(id);       // isi ke input modal
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
