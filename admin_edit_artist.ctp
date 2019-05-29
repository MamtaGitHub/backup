<?php
$st_date = $this->request->data['User']['start_date'];
$ex_date = $this->request->data['User']['end_date'];
$new_st_date = date('m/d/Y', strtotime($st_date));
$new_ex_date = date('m/d/Y', strtotime($ex_date));

?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= $layoutTitle ?>
        </h1>
        <?php
       
        if($utype == 1){
        
        }
        else{ ?>
            <ol class="breadcrumb">
            <li><i class="fa fa-home"></i> <?php
                echo $this->Html->getCrumbs(' > ', array(
                    'text' => 'Home',
                    'url' => array('controller' => 'users', 'action' => 'dashboard', 'admin' => true),
                    'escape' => false
                ));
                ?> 
            </li>
            <li><?php
                echo $this->Html->getCrumbs(' > ', array(
                    'text' => 'List Artist',
                    'url' => array('controller' => 'Users', 'action' => 'listArtistuser', 'admin' => true),
                    'escape' => false
                ));
                ?> 
            </li>
            <li class="active"><?= $layoutTitle ?></li>
        </ol>

      <?php  }
        ?>

        
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="box box-primary">
            <?php echo $this->Session->flash(); ?>
            <div class="row">
                <div class="col-xs-6">
                      <?php  echo $this->Form->create('User', 
                        array('class'=>'horizontal-form form-validation',
                            'enctype'=>'multipart/form-data','novalidate'=>true)); ?>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Name</label>
                            <?php echo $this->Form->input('name',array('label'=>false,'placeholder'=>'Enter Name','class'=>'form-control','required'=>true,'maxlength'=>'20')); ?>
                        </div>
                    <?php if($utype == 3){ ?>
                         <div class="form-group">
                            <label for="exampleInputEmail1">New password</label>
                            <?php echo $this->Form->input('newpassword',array('type'=>'password','label'=>false,'class'=>'form-control')); ?>
                        </div>
                        <?php } ?>
                       
                        <div class="form-group">
                            <label for="exampleInputEmail1">Phone</label>
                            <?php echo $this->Form->input('phone',
                                    array('label'=>false,
                                        'placeholder'=>'Enter Phone',
                                        'class'=>'form-control',
                                        'required'=>true,
                                        'type'=>'number',
                                        'min'=>'1'
                                        )); ?>
                        </div>
                        <?php if($utype == 3){ ?>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Email</label>
                            <?php echo $this->Form->input('email',
                                    array('label'=>false,
                                        'placeholder'=>'Enter Email',
                                        'class'=>'form-control',
                                        'required'=>true,
                                        'type'=>'email'
                                        )); 
                            ?>
                        </div>
                        <?php } ?>
                         <div class="form-group">
                            <label for="exampleInputEmail1">Address</label>
                            <?php echo $this->Form->input('address',
                                    array('label'=>false,
                                        'placeholder'=>'Enter Address',
                                        'class'=>'form-control',
                                        'required'=>true,'maxlength'=>'200'));
                            ?>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Profile image</label>
                            <?php echo $this->Form->input('image', array('type' => 'file', 
                                'label' => false, 
                                'class' => 'form-control',
                                'accept'=>'image/x-png,image/gif,image/jpeg'
                                )
                                    ); ?>

                            <?php if (!empty($this->request->data['User']['image'])) {
                                ?>
                                <input type="hidden" value="<?php echo $this->request->data['User']['image'] ?>" name = "data[User][oldImage]">
                                <?php
                                        $m = FULL_BASE_URL . $this->webroot .$this->request->data['User']['image'];
                                        $default = FULL_BASE_URL . $this->webroot . 'img/users/default.png';
                                        //prx( $default);
                                        if (!empty($this->request->data['User']['image'])) {
                                            $img = $this->Html->image($m, array('alt' => 'User Image', 'border' => '1',
                                                'height' => '120', 'width' => '120', 'class'=>'abc','data-src' => 'holder.js/100%x100'));
                                            echo $img;
                                        } else {
                                            $img = $this->Html->image($default, array('alt' => 'default', 'border' => '1',
                                                'height' => '60', 'width' => '60', 'class'=>'abc','data-src' => 'holder.js/100%x100'));
                                            echo $img;
                                        }
                                    ?>
                              <!--   <a href= "<?php ///echo FULL_BASE_URL . $this->webroot . $this->request->data['User']['image'] ?>">Image</a>  -->

                            <?php } ?>
                           <p class="formated_txt"><?= __('Upload only jpg, jpeg, png files') ?></p>
                        </div> 
                        <div class="form-group">

                            <div>
                                <?php
                                $images = explode(',', $this->request->data['User']['gallery_image']);

                                //pr($images);
                                //pr($this->request->data['Feed']['image']); 
                                ?>

                                <label for="exampleInputEmail1">Gallery images</label>
                                <div id="filediv">
                                    
                                    <?php
                                    //$temp = explode(',',$this->request->data['Feed']['image']);

                                    foreach ($images as $image) {
                                        ?>
                                        <div cl-imng="
                                             <?php echo $image ?>" 
                                             id-img="<?= $this->request->data['User']['gallery_image'] ?>" accept="image/x-png,image/jpg,image/jpeg" >
                                            <?php if(!empty($image)){ ?>
                                                
                                                <img src="<?php echo $this->webroot . $image; ?>" 
                                                 style="height:100px;width:100px;" >

                                            <img src ="<?php echo FULL_BASE_URL . $this->webroot . 'img/x.png' ?>" 
                                                 class="remove-img"/>
                                                
                                           <?php } ?>
                                            
                                           <input type="hidden" name="data[User][mage][]" value="<?= $image ?>">
                                        </div>
                                        <?php
                                    }
                                    ?>
                                   
                                </div>
                                <br/>

                                <input type="button" id="add_more" class="upload" value="Add More Files"/>

                                <br/>   
                                <p class="formated_txt"><?= __('Select multiples images for Artist only in jpeg, jpg, png format') ?></p>
                            </div>
                            
                        </div>
                        <div class="form-group">
                            <label class="control-label">Add video</label>
                            <?php echo $this->Form->input('video', array('type' => 'file', 'label' => false, 'class' => 'form-control','accept'=>'video/mp4')); ?>
                            <?php if (!empty($this->request->data['User']['video'])): ?>
                                <input type="hidden" value = "<?php echo $this->request->data['User']['video']; ?>" name="data[User][videos]">

                                <a href = "<?php echo FULL_BASE_URL . $this->webroot . $this->request->data['User']['video']; ?>"target="_blank">video</a>
                            <?php endif; ?>

                            <p class="formated_txt"><?= __('Upload only mp4 file') ?></p>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Video thumbnail</label>
                            <?php echo $this->Form->input('video_thumbnail', array('type' => 'file', 'label' => false, 'class' => 'form-control','accept'=>'image/x-png,image/jpg,image/jpeg')); ?>

                            <?php if (!empty($this->request->data['User']['video_thumbnail'])) {
                                ?>
                                <input type="hidden" value="<?php echo $this->request->data['User']['video_thumbnail'] ?>" name = "data[User][oldvideo_thumbnail]">
                                 <?php
                                        $m = FULL_BASE_URL . $this->webroot . $this->request->data['User']['video_thumbnail'];
                                        $default = FULL_BASE_URL . $this->webroot . 'img/users/default.png';
                                        //prx( $default);
                                        if (!empty($this->request->data['User']['video_thumbnail'])) {
                                            $img = $this->Html->image($m, array('alt' => 'Video Thumbnail', 'border' => '1',
                                                'height' => '120', 'width' => '120', 'class'=>'abc', 'data-src' => 'holder.js/100%x100'));
                                            echo $img;
                                        } else {
                                            $img = $this->Html->image($default, array('alt' => 'default', 'border' => '1',
                                                'height' => '60', 'width' => '60', 'class'=>'abc', 'data-src' => 'holder.js/100%x100'));
                                            echo $img;
                                        }
                                    ?>
                                <!-- <a href= "<?php// echo FULL_BASE_URL . $this->webroot . $this->request->data['User']['video_thumbnail'] ?>">Thumbnail</a> --> 

                            <?php } ?>
                          <p class="formated_txt"><?= __('Upload only jpg, jpeg, png files') ?></p>
                        </div> 
                        <?php if($utype == 3){ ?>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Status</label>
                                <?php
                                $status = array('Deactivate', 'Activate');
                                echo $this->Form->input('status', 
                                array('label' => false, 
                                'class' => 'form-control',
                                'empty' => 'Select Status',
                                'options' => $status,
                                'required'=>true, 
                                )
                                );
                                ?>
                            </div>

                        <?php } ?>
                        <?php if($this->request->data['User']['guest_user_status']=='1'){ ?>
                        <div class="custom-input-default-check">
                        <strong>Are you guest User?:</strong>
                <input id="coupon_question" type="checkbox" name="data[User][guest_user_status]" onchange="valueChanged()" checked="checked" />  
                        </div>
                        <?php 
                        if(!empty($this->request->data['User']['start_date'])){
                           $val = $new_st_date.'-'. $new_ex_date;
                        }else{
                             $val = 'Select you date';
                        }


                        ?>

                          <div class="form-group" id="guestdate">
                            <label>Start date</label>
                            <input type="text" name="daterange" value="<?php echo $new_st_date.'-'. $new_ex_date; ?>" class="with_date" />
                          
                         </div>
                     <?php } else { ?>
                     <div class="custom-input-default-check">
                     <strong>Are you guest User?:</strong>
                            <input id="coupon_question" type="checkbox" name="data[User][guest_user_status]" onchange="valueChanged()" />  
                     </div>

                          <div class="form-group" id="guestdate" style="display:none;">
                            <label>Start date</label>
                             <input type="text" name="daterange"/>
                        </div> 
                         <?php } ?>

                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                    <?php echo $this->Form->end(); ?>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script> -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script>
// $(function() {
    jQuery(document).ready(function(){
       
    if ($('input[name="daterange"]').val() != ''){
         var today = new Date();
        var value = $('input[name="daterange"]').val();
        var arrval =  value.split('-');
    }
    else{
        var today = '05/16/2018-05/16/2018';

       // console.log(today);
        var arrval =  today.split('-');

    }
    console.log(value);
  $('input[name="daterange"]').daterangepicker({
    opens: 'left',
    minDate:today,
    startDate: arrval['0'],
    endDate: arrval['1'],
   
  }
  , function(start, end, label) {
    //console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
  });

    });
  //   var value = $('input[name="daterange"]').val();
  //   console.log(value);
  // $('input[name="daterange"]').daterangepicker({
  //   opens: 'left'
  // }
  // , function(start, end, label) {
  //   //console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
  // });
// });
</script>
<script type="text/javascript">

function valueChanged()
{
    if($('#coupon_question').is(":checked"))   
       
        $("#guestdate").show();
    else
       
        $("#guestdate").hide();
}

</script>

<script>
    var abc = 0; //Declaring and defining global increement variable

    $(document).ready(function () {

//To add new input file field dynamically, on click of "Add More Files" button below function will be executed
        $('#add_more').click(function () {
            $(this).before($("<div/>", {id: 'filediv'}).fadeIn('slow').append(
                    $("<input/>", {name: 'data[User][gallery_image][]', type: 'file', class: 'form-control file', accept: 'image/x-png,image/gif,image/jpeg'}),
                    $("<br/><br/>")
                    ));
        });

       //following function will executes on change event of file input to select different file
        $('body').on('change', '.file', function () {
            if (this.files && this.files[0]) {
                abc += 1; //increementing global variable by 1

                var z = abc - 1;
                var x = $(this).parent().find('#previewimg' + z).remove();
                $(this).before("<div id='abcd" + abc + "' class='abcd'><img id='previewimg" + abc + "' src=''/></div>");

                var reader = new FileReader();
                reader.onload = imageIsLoaded;
                reader.readAsDataURL(this.files[0]);

                $(this).hide();
                $("#abcd" + abc).append($("<img/>", {id: 'img', src: "<?php echo FULL_BASE_URL . $this->webroot . 'img/x.png'; ?>", alt: 'delete'}).click(function () {
                    $(this).parent().parent().remove();
                }));
            }
        });

        //To preview image
        function imageIsLoaded(e) {
            $('#previewimg' + abc).attr('src', e.target.result);
        };

    $('#upload').click(function (e) {
        var name = $(":file").val();
        if (!name)
        {
            alert("First Image Must Be Selected");
            e.preventDefault();
        }
    });

    $('.remove-img').on('click', function () {
        $(this).parent().remove();
    });
    });
</script>


<!-- /.content-wrapper -->
<style>
    .abcd img{
        height:100px;
        width:100px;
        padding: 5px;
        border: 1px solid rgb(232, 222, 189);
    }
    #img{
        width: 30px;
        border: none;
        height:30px;
        margin-left: -20px;
        margin-bottom: 91px;
    }
    .upload {
        
        width: 25%;
    }
    div#filediv img {
        margin: 2px 5px 6px 7px;
    }
    img.abc{
    padding-top:5px;
    }

</style>