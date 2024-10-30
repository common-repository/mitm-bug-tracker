<?php
if (isset($_REQUEST['browser_id']) && $_REQUEST['browser_id']) {
  $_result = $this->wpdb->get_results("SELECT * FROM {$this->wpdb->prefix}mitm_browser WHERE browser_id='{$_REQUEST['browser_id']}'");
  $_result = $_result[0];
  $browser_name = esc_attr($_result->browser_name);
}
?>

<div class="wrap">
  <div id="icon-options-general" class="icon32 icon32-posts-post"><br>
  </div>
  <h2>Save Browser</h2>
  <div id="message" class="below-h2"></div>
  <form name="browser_add" method="post" action="" enctype="multipart/form-data">
    <?php 
    if (isset($_REQUEST['browser_id']) && $_REQUEST['browser_id']) {
      echo '<input type="hidden" name="browser_id" value="' . esc_attr($_REQUEST['browser_id']) . '" />';
    }
    ?>
    <table class="form-table">
      <tbody>
        <tr>
          <th><label for="browser_name">Browser Name</label></th>
          <td><input name="browser_name" type="text" id="browser_name" value="<?php echo $browser_name; ?>" class="regular-text required" /></td>
        </tr>
      </tbody>
    </table>
    <p class="submit">
      <input type="submit" name="submit" id="submit" class="button-primary" value="Save Browser">
      <a href="admin.php?page=mitm_bugtracker_browser_view" class="button-secondary">Cancel</a> </p>
  </form>
</div>
<style>
  tr.error th, tr.error td {background-color: #FFEBE8!important; border-bottom: solid 1px #CCC!important;}
  tr.error td .required {border-color: #C00!important;}
</style>
