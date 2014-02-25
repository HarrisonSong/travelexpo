<?php $this->load->view('header.php');?>
<?php $this->load->view('navbar.php');?>
 <div class="map-wrap">
  <div class="map-google">
    <iframe class="map-content" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="<?php echo site_url("general/map_iframe")?>">
    </iframe>
  </div>
  <div class="friends-profile-wrap">
    <div class="slide-button">

    </div>
  </div>
</div>
<?php $this->load->view('footer.php');?>