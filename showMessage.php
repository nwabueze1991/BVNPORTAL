<?php
function showMessageError($message){
  echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>'.$message.'</strong> Check the field(s) below.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
}

function showMessageSuccess($message){
  echo '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>'.$message.'</strong> Check the field(s) below.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
}

function showMessageTimeout($message){
  echo '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>'.$message.'</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
}
 ?>
