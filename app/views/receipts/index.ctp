<div class="blocks form">
    <h2><?php echo 'ரசீதுகள்'; ?></h2>
    <div class="actions">
    	<ul>
            <li><?php echo $html->link(__('வீட்டு வரி ரசீது', true), array('action'=>'housetax_index')); ?></li>
      </ul>
      <ul>
          <li><?php echo $html->link(__('குடிநீர் வரி ரசீது', true), array('action'=>'watertax_index')); ?></li>
      </ul>
      <ul>
            <li><?php echo $html->link(__('தொழில் வரி ரசீது', true), array('action'=>'professionaltax_index')); ?></li>
      </ul>
      <ul>
            <li><?php echo $html->link(__('டி & ஓ வியாபாரிகள் வரி ரசீது', true), array('action'=>'dotax_index')); ?></li>
      </ul>
      <ul>
            <li><?php echo $html->link(__('மற்ற ரசீதுகள்', true), array('action'=>'otherstax_index')); ?></li>
      </ul>
    </div>
</div>