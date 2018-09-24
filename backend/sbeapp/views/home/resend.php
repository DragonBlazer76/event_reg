<div ng-controller="home">
    <p>
    <?php 
    echo $this->errorCode.'<br />';
    if( $this->errorCode!="" ){
        echo $this->error;
    }
    ?>
    </p>
    
    <?php if( $this->errorCode=="INVALID_CODE" || $this->errorCode=="EXPIRED_CODE" ){ ?>
    <p>
        <a href="<?php echo $APPVARS->siteUrl.'resend?email='.$this->email;?>">Resend verification link</a>
    </p>        
    <?php } ?>
</div>