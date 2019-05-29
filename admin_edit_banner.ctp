<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?= $layoutTitle ?>
        </h1>
        <ol class="breadcrumb">
            <li><i class="fa fa-home"></i> <?php
                echo $this->Html->getCrumbs(' > ', array(
                    'text' => 'Home',
                    'url' => array('controller' => 'Users', 'action' => 'dashboard', 'admin' => true),
                    'escape' => false
                ));
                ?>  
            </li>
            <li class="active"><?= $layoutTitle ?></li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="box box-primary">
            <?php echo $this->Session->flash(); ?>
        <div class="row">
            <div class="col-xs-6">
                <?php  echo $this->Form->create('Banner', array('class'=>'horizontal-form form-validation','enctype'=>'multipart/form-data','novalidate')); ?>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Title</label>
                            <?php echo $this->Form->input('name',array('label'=>false,'placeholder'=>'Enter Title','class'=>'form-control','maxlength'=>'20')); ?>
                        </div>
                        
                       
                         <div class="form-group">
                    <label class="control-label">Image</label>
                    <?php echo $this->Form->input('image',array('type'=>'file','label'=>false,'class'=>'form-control','accept' => 'image/*')); ?>
                    
                    <?php if(!empty($this->request->data['Banner']['image'])){
                    ?>
                    <input type="hidden" value="<?php echo $this->request->data['Banner']['image'] ?>" name = "data[Banner][oldImage]">
                     <?php
                                        $m = FULL_BASE_URL . $this->webroot . $this->request->data['Banner']['image'];
                                        $default = FULL_BASE_URL . $this->webroot . 'img/users/default.png';
                                        //prx( $default);
                                        if (!empty($this->request->data['Banner']['image'])) {
                                            $img = $this->Html->image($m, array('alt' => 'User Image', 'border' => '1',
                                                'height' => '120', 'width' => '120', 'data-src' => 'holder.js/100%x100'));
                                            echo $img;
                                        } else {
                                            $img = $this->Html->image($default, array('alt' => 'default', 'border' => '1',
                                                'height' => '60', 'width' => '60', 'data-src' => 'holder.js/100%x100'));
                                            echo $img;
                                        }
                                    ?>
                    <!-- <a href= "<?php// echo FULL_BASE_URL.$this->webroot.$this->request->data['Banner']['image'] ?>">Image</a> --> 
                    
                <?php } ?>
                <br/>
                <span class="formated">Upload only jpg, jpeg, png files</span>
            </div> 
        

                    
                 
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                <?php echo $this->Form->end(); ?>
                </div>
                    <!-- /.box -->
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
