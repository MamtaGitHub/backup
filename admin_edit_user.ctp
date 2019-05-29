<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?=$layoutTitle?>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="box box-primary">
            <?php echo $this->Session->flash(); ?>
        <div class="row">
            <div class="col-xs-6">
                <?php  echo $this->Form->create('User', array('class'=>'horizontal-form form-validation','enctype'=>'multipart/form-data'));
				echo $this->Form->hidden('ID',array('class'=>'horizontal-form form-validation'));
				?>
        <div class="box-body">
            <div class="form-group">
                <label for="exampleInputEmail1">First name</label>
                <?php echo $this->Form->input('fname',array('label'=>false,'placeholder'=>'Enter first name','class'=>'form-control')); ?>
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Last name</label>
                <?php echo $this->Form->input('lname',array('label'=>false,'placeholder'=>'Enter last name','class'=>'form-control')); ?>
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">Email</label>
                <?php echo $this->Form->input('email',array('label'=>false,'type'=>'email','placeholder'=>'Enter email','class'=>'form-control')); ?>
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">phone</label>
                <?php echo $this->Form->input('phone',array('label'=>false,'placeholder'=>'Enter phone','class'=>'form-control')); ?>
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">Status</label>
                <?php
                $status = array('Disable','Enable');
                echo $this->Form->input('status',array('label'=>false,'placeholder'=>'','class'=>'form-control','options'=> $status,'empty'=> '--Select--')); ?>
            </div>
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
<!-- /.content-wrapper -->
