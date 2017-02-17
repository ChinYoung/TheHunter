<?php if (strlen($_SESSION['error']) > 0) { ?>
                    <div class="alert alert-block alert-error fade in">
                        <button data-dismiss="alert" class="close" type="button">x</button>
                        <h4 class="alert-heading">Error!</h4>
                        <p><?php echo $_SESSION['error']; ?></p>
                    </div>
<?php } elseif (strlen($_SESSION['notice']) > 0) { ?>
                    <div class="alert alert-block alert-success fade in">
                        <button data-dismiss="alert" class="close" type="button">x</button>
                        <h4 class="alert-heading">Success!</h4>
                        <p><?php echo $_SESSION['notice']; ?></p>
                    </div>
<?php } ?>
<?php foreach (array('status','error','notice') AS $k) { $_SESSION[$k] = ''; } ?>