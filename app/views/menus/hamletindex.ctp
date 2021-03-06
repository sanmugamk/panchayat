
<div class="blocks form">
    <h2><?php __('குக்கிராமங்கள்'); ?></h2>
    <div class="actions">
        <ul>
            <li><?php echo $html->link(__('புதிய குக்கிராமத்தை சேர்', true), array('action'=>'addhamlet')); ?></li>
        </ul>
    </div>
    <table cellpadding="0" cellspacing="0">
        <?php
            $tableHeaders = $html->tableHeaders(array(
                $paginator->sort('குக்கிராமத்தின் குறியீடு', 'hamlet_code'),
                $paginator->sort('குக்கிராமத்தின் பெயர்', 'hamlet_name'),
                $paginator->sort('விவரிப்பு', 'description'),
                				__('செயல்கள்', true),
            ));
            echo $tableHeaders;
    
            $rows = array();
            foreach ($hamlets AS $hamlet) {
                $actions = ' ' . $html->link(__('திருத்து', true), array(
                	'action' => 'edithamlet',
                	$hamlet['Hamlet']['id']));
                $actions .= ' ' . $html->link(__('நீக்கு', true), array(
                  'action' => 'deletehamlet', $hamlet['Hamlet']['id']),
                	null, __('கண்டிப்பாக நீக்க விரும்புகிறீர்களா?', true)
								);
                $rows[] = array(
                    $hamlet['Hamlet']['hamlet_code'],
                    $hamlet['Hamlet']['hamlet_name'],
                    $hamlet['Hamlet']['description'],
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