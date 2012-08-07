
<div class="blocks form">
    <h2><?php echo 'வேலையின் விபரங்கள்'; ?></h2>
    <div class="actions">
        <ul>
            <li><?php echo $html->link(__('புதிய வேலையின் விபரங்களைச் சேர்', true), array('action'=>'add_workdetails')); ?></li>
        </ul>
    </div>
    <table cellpadding="0" cellspacing="0">
        <?php
            $tableHeaders = $html->tableHeaders(array(
                $paginator->sort('தேதி', 'year'),
                $paginator->sort('வேலையின் பெயர்', 'name_of_work'),
                $paginator->sort('மதிப்பீடு செய்யப்பட்ட தொகை', 'estimation_amount'),
                $paginator->sort('AS எண்', 'as_number'),
                				__('செயல்கள்', true),
            ));
            echo $tableHeaders;
    
            $rows = array();
            foreach ($workdetails AS $workdetail) {
                $actions = ' ' . $html->link(__('திருத்து', true), array(
                	'action' => 'edit_workdetails',
                	$workdetail['Workdetail']['id']));
                $actions .= ', ' . $html->link(__('நீக்கு', true), array(
                  'action' => 'delete_workdetails', $workdetail['Workdetail']['id']), 
                	null, __('கண்டிப்பாக நீக்க விரும்புகிறீர்களா?', true)
								);
                $rows[] = array(
                  $workdetail['Workdetail']['year'],
                  $workdetail['Workdetail']['name_of_work'],
                  $workdetail['Workdetail']['estimation_amount'],
                  $workdetail['Workdetail']['as_number'],
                  $actions,
                );
            }
    
            echo $html->tableCells($rows);
            //echo $tableHeaders;
        ?>
    </table>
</div>

<div class="paging"><?php echo $paginator->numbers(); ?></div>
<div class="counter"><?php echo $paginator->counter(array('format' => __('பக்கம் %pages%இல் %page%, இங்கே தெரிவது மொத்தம் %count%இல் %current% பதிவேடு(கள்), ஆரம்பப் பதிவேடு எண் %start%, இறுதிப் பதிவேடு எண் %end%', true))); ?></div>