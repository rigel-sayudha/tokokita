<div class="container-fluid pt-5">
        <div class="text-center mb-4">
            <h2 class="section-title px-5"><span class="px-2">Form Edit Toko</span></h2>
        </div>
        <div class="row px-xl-5">
            <div class="col-lg-7 mb-5">
                <div class="contact-form">   
                    <form name="sentMessage"  method="post" action="<?php echo site_url('toko/edit');?>" enctype="multipart/form-data">
                        <div class="control-group">
                        <input type="hidden" name="idToko" value="<?php echo $toko->idToko; ?>"/>
                            <input type="text" name="namaToko" class="form-control" id="name" placeholder="Nama Toko"
                                required="required" data-validation-required-message="Please enter your name" value="<?php echo $toko->namaToko; ?>"/>
                            <p class="help-block text-danger"></p>
                           
                        </div>
				
                        <div class="control-group">
                            <input type="file" name="logo" class="form-control" id="emfail" placeholder="Logo"
                                data-validation-required-message="Please enter your email" />
                            <p class="help-block text-danger"></p>
                            <img src="<?php echo base_url()."/assets/logo_toko/".$toko->logo;?>" width="100"/><br><br>
                        </div>
                        <div class="control-group">
                            <textarea class="form-control" rows="3" id="message" name="deskripsi" placeholder="Deskripsi"
                                required="required"
                                data-validation-required-message="Please enter your message"><?php echo $toko->deskripsi; ?></textarea>
                            <p class="help-block text-danger"></p>
                        </div>
                        <div>
                            <button class="btn btn-primary py-2 px-4" type="submit" id="sendMesrsageButton">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>