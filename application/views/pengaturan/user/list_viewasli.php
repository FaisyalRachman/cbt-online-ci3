<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		Data User
	
	</h1>
	<ol class="breadcrumb">
	
		<li class="active">Hak Akses</li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
	<div class="row">
    <div class="container-fluid">
                <div class="card">
                    <div class="card-header with-border">
    						<div class="card-title">Data User</div>
    						<div class="card-tools pull-right">
    							<div class="dropdown pull-right">
    								<a class="btn btn-outline-success btn-sm" href="<?php echo current_url(); ?>/index/add">Tambah User</a>
    							</div>
    						</div>
                    </div><!-- /.card-header -->

                    <div class="card-body">
                        <table id="table-user" class="table table-bordered table-hover table-sm">
                        <thead class="table table-primary">
                                <tr>
                                    <th>No.</th>
                                    <th>Username</th>
                                    <th>Nama</th>
                                    <th>Level</th>
									<th>Opsi 1</th>
									<th>Opsi 2</th>
									<th>Keterangan</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td> </td>
                                    <td> </td>
                                    <td> </td>
                                    <td> </td>
                                    <td> </td>
									<td> </td>
                                    <td> </td>
                                </tr>
                            </tbody>
                        </table>                        
                    </div>
                </div>
        </div>
    </div>
</section><!-- /.content -->

<script lang="javascript">
    $(function(){
        $('#table-user').DataTable({
                  "paging": true,
                  "iDisplayLength":10,
                  "bProcessing": false,
                  "bServerSide": true, 
                  "searching": true,
                  "aoColumns": [
    					{"bSearchable": false, "bSortable": false, "sWidth":"20px"},
    					{"bSearchable": false, "bSortable": false, "sWidth":"40px"},
    					{"bSearchable": false, "bSortable": false, "sWidth":"100px"},
                        {"bSearchable": false, "bSortable": false},
						{"bSearchable": false, "bSortable": false},
						{"bSearchable": false, "bSortable": false},
						{"bSearchable": false, "bSortable": false},
                        {"bSearchable": false, "bSortable": false, "sWidth":"30px"}],
                  "sAjaxSource": "<?php echo current_url();?>/get_all_user/",
                  "autoWidth": false
         });          
    });
</script>