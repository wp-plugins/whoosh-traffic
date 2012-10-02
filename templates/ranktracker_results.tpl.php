<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<script type="application/x-javascript">
function getID(obj) {
    return $(obj).attr('href').substr(1);
}

$(document).ready(function() {
    $('.submitdelete').click(function() {
        if (!confirm( 'You are about to delete this pair \n  \'Cancel\' to stop, \'OK\' to delete.' ) ) {
            return false;
        }
        
        var ID = getID(this);
        
        var data = {
            action: '<?php echo $this->plugin_id;?>_delete_pair',
            pair_id: ID
        };
        
        $.post(ajaxurl, data, function (resp) {
            if (!resp['errors']) {
                $('<div id="message" class="updated below-h2"><p>Pair Deleted</p></div>').insertBefore('#poststuff');
                
                $('#row-'+ID).remove();
            } else {
                $('<div id="message" class="updated below-h2"><p>'+resp['message']+'</p></div>').insertBefore('#poststuff');
            }
        });
    });
});
</script>
<div class="wrap">
  <h1><?PHP echo ucwords($this->type); ?> Results</h1>
  <!-- <h2><?php _e($title); ?><a href="/wp-admin/admin.php?page=ranktracker_add_new" class="add-new-h2">Add New</a></h2> -->
  <br class="clear"/>
  <div id="poststuff" class="metabox-holder">
    <?php $i = 1; ?>
    <?php foreach($this->items as $key => $value): ?>
    <div class="stuffbox">
    <h3><?php echo $key; ?></h3>
      <div class="inside">
        <table class="wp-list-table widefat fixed" cellspacing="0">
          <thead>
            <tr>
              <th scope="col" class="manage-column" width="45%">URL</th>
              <th scope="col" class="manage-column" width="40%">Keyword</th>
              <th scope="col" class="manage-column" width="4%">Rank</th>
              <th scope="col" class="manage-column" width="5%">Change</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach($value as $pair): ?>
            <tr class="alternate" id="row-<?php echo $pair['id']; ?>">
              <td>
                <strong class="<?php echo $this->plugin_id ?>_details"><?php echo $pair['url']; ?></strong>
                <div class="row-actions">
                  <span class="inline"><a href="/wp-admin/admin.php?page=ranktracker_details_page&pair_id=<?php echo $pair['id']; ?>" class="display-graph" title="View Graph">View</a> | </span>
                  <!--<span class="inline"><a href="#<?php echo $pair['id']; ?>" class="editinline" title="Edit this item inline">Edit</a> | </span>-->
                  <span class="inline hide-if-no-js trash"><a class="submitdelete" title="Delete this item" href="#<?php echo $pair['id']; ?>">Delete</a></span>
                </div>
              </td>
              <td><?php echo $pair['keyword']; ?></td>
              <td align="center">
                <strong><?php echo $pair['rank']; ?></strong>
              </td>
              <td align="center">
                <strong><?php echo $pair['change']; ?></strong>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>