$ImClass = new ItemsManager;
$items = $ImClass->getItemsAdmin();
if(!isset($_GET['item_type']) || $item_type == "view")
{
    if (!empty($items)) 
    {
        echo '<h3>All '.IMTITLE.'</h3><table class="highlight">';
        foreach ($items as $item) 
        {
            $id = basename($item, ".xml");
            $file = ITEMDATA . $item;
            $data = @getXML($file); // TODO: this is a security risk!
            $date = $data->property->date;
            $title = html_entity_decode($data->property->title, ENT_QUOTES, 'UTF-8');
            $label = html_entity_decode((string) $data->field->{$item_labelfield}, ENT_QUOTES, 'UTF-8');
            if ($label == "")
                $label = $title;
            ?>
            <tr>
                <td>
                    <a href="load.php?id=item_manager&edit=<?php echo $id; ?>" title="Edit <?php echo IMTITLE; ?>: <?php echo $label; ?>">
                    <?php echo $label; ?>
                    </a>
                    <span style="font-size:9px; color:#a0a0a0; margin-left:5px"><?php echo $data->property->category;?></span>
                </td>
                <td style="text-align: right;">
                    <span><?php echo $date; ?></span>
                </td>
                <td class="switch_visible">
                    <a href="load.php?id=item_manager&visible=<?php echo $id; ?>" class="switch_visible" style="text-decoration:none" title="Visible <?php echo IMTITLE; ?>: <?php echo $label; ?>?">
                    <?php 
                    if (!isset($data->property->visible) || $data->property->visible == true)
                    { 
                        echo '<font color="#333333">V</font>';
                    }
                    else
                    {
                        echo '<font color="#acacac">V</font>';
                    }
                    ?>
                    </a>
                </td>
                <td class="switch_promo">
                    <a href="load.php?id=item_manager&promo=<?php echo $id; ?>" class="switch_promo" style="text-decoration:none" title="Promo <?php echo IMTITLE; ?>: <?php echo $title; ?>?">
                    <?php 
                    if (!isset($data->property->promo) || $data->property->promo == true)
                    {  
                        echo '<font color="#333333">P</font>';
                    }
                    else
                    {
                        echo '<font color="#acacac">P</font>';
                    }
                    ?>
                    </a>
                </td>
                <td class="delete">
                    <a href="load.php?id=item_manager&delete=<?php echo $id; ?>" class="delconfirm" title="Delete <?php echo IMTITLE; ?>: <?php echo $title; ?>?">
                    X
                    </a>
                </td>
            </tr>
            <?php
        }
        echo '</table>';
    }
}
echo '<p><b>' . count($items) . '</b> '.IMTITLE.'</p>';

