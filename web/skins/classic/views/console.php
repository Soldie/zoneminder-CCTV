<?php
$servers = Server::find_all();
require_once('includes/Storage.php');
$storage_areas = Storage::find_all();
$show_storage_areas = count($storage_areas) > 1 and canEdit( 'System' ) ? 1 : 0;

xhtmlHeaders( __FILE__, translate('Console') );
?>
<body>
    <form name="monitorForm" method="get" action="<?php echo $_SERVER['PHP_SELF'] ?>">
    <input type="hidden" name="view" value="<?php echo $view ?>"/>
    <input type="hidden" name="action" value=""/>
    <?php include("skins/$skin/views/header.php") ?>
    <div id="content" class="container-fluid">
      <div id="consoleTable" cellspacing="0">
        <div class="thead">
          <div class="tr">
					<div class="MonitorInfo">
            <div class="colName"><?php echo translate('Name') ?></div>
            <div class="colFunction"><?php echo translate('Function') ?></div>
<?php if ( count($servers) ) { ?>
			<div class="colServer"><?php echo translate('Server') ?></div>
<?php } ?>
            <div class="colSource"><?php echo translate('Source') ?></div>
<?php if ( $show_storage_areas ) { ?>
            <div class="colStorage"><?php echo translate('Storage') ?></div>
<?php } ?>
</div>
<div class="Events">
<?php
for ( $i = 0; $i < count($eventCounts); $i++ )
{
?>
            <div class="colEvents <?php echo $eventCounts[$i]['title'] ?>"><?php echo $eventCounts[$i]['title'] ?></div>
<?php
}
?>
</div>
            <div class="colZones"><?php echo translate('Zones') ?></div>
<?php
if ( canEdit('Monitors') )
{
?>
            <div class="colOrder"><?php echo translate('Order') ?></div>
<?php
}
?>
            <div class="colMark"><?php echo translate('Mark') ?></div>
          </div>
        </div>
        <div class="tfoot">
          <div class="tr">
            <div class="colLeftButtons" colspan="<?php $columns = 3;
 if ( count($servers) > 1 ) { $columns += 1; }
 if ( $show_storage_areas ) { $columns += 1; }
echo $columns;
 ?>">
              <input type="button" name="addBtn" value="<?php echo translate('AddNewMonitor') ?>" onclick="addMonitor( this )"/>
              <!-- <?php echo makePopupButton( '?view=monitor', 'zmMonitor0', 'monitor', translate('AddNewMonitor'), (canEdit( 'Monitors' ) && !$user['MonitorIds']) ) ?> -->
            </div>
			<div class="Events">
<?php
for ( $i = 0; $i < count($eventCounts); $i++ )
{
    parseFilter( $eventCounts[$i]['filter'] );
?>
            <div class="colEvents <?php echo $eventCounts[$i]['title'] ?>"><?php echo makePopupLink( '?view='.$eventsView.'&amp;page=1'.$eventCounts[$i]['filter']['query'], $eventsWindow, $eventsView, $eventCounts[$i]['total'], canView( 'Events' ) ) ?></div>
<?php
}
?>
			</div>
            <div class="colZones"><?php echo $zoneCount ?></div>
            <div class="colRightButtons" colspan="<?php echo canEdit('Monitors')?2:1 ?>"><input class="btn btn-default" type="button" name="editBtn" value="<?php echo translate('Edit') ?>" onclick="editMonitor( this )" disabled="disabled"/><input class="btn btn-default" type="button" name="deleteBtn" value="<?php echo translate('Delete') ?>" onclick="deleteMonitor( this )" disabled="disabled"/></div>
          </div>
        </div>
        <div class="tbody">
<?php
foreach( $displayMonitors as $monitor )
{
?>
          <div class="tr" title="<?php echo $monitor['Id'] ?>">
<?php
    if ( !$monitor['zmc'] )
        $dclass = "errorText";
    else
    {
    // https://github.com/ZoneMinder/ZoneMinder/issues/1082
        if ( !$monitor['zma'] && $monitor['Function']!='Monitor' )
            $dclass = "warnText";
        else
            $dclass = "infoText";
    }
    if ( $monitor['Function'] == 'None' )
        $fclass = "errorText";
    //elseif ( $monitor['Function'] == 'Monitor' )
     //   $fclass = "warnText";
    else
        $fclass = "infoText";
    if ( !$monitor['Enabled'] )
        $fclass .= " disabledText";
    $scale = max( reScale( SCALE_BASE, $monitor['DefaultScale'], ZM_WEB_DEFAULT_SCALE ), SCALE_BASE );
?>
<div class="MonitorInfo">
            <div class="colName"><?php echo makePopupLink( '?view=watch&amp;mid='.$monitor['Id'], 'zmWatch'.$monitor['Id'], array( 'watch', reScale( $monitor['Width'], $scale ), reScale( $monitor['Height'], $scale ) ), $monitor['Name'], $running && ($monitor['Function'] != 'None') && canView( 'Stream' ) ) ?></div>
            <div class="colFunction"><?php echo makePopupLink( '?view=function&amp;mid='.$monitor['Id'], 'zmFunction', 'function', '<span class="'.$fclass.'">'.translate('Fn'.$monitor['Function']).( empty($monitor['Enabled']) ? ', disabled' : '' ) .'</span>', canEdit( 'Monitors' ) ) ?></div>
<?php if ( count($servers) ) { ?>
			<div class="colServer"><?php 
$Server = new Server( $monitor['ServerId'] );
echo $Server->Name();
 ?></div>
<?php } ?>
<?php if ( $monitor['Type'] == "Local" ) { ?>
            <div class="colSource"><?php echo makePopupLink( '?view=monitor&amp;mid='.$monitor['Id'], 'zmMonitor'.$monitor['Id'], 'monitor', '<span class="'.$dclass.'">'.$monitor['Device'].' ('.$monitor['Channel'].')</span>', canEdit( 'Monitors' ) ) ?></div>
<?php } elseif ( $monitor['Type'] == "Remote" ) { ?>
            <div class="colSource"><?php echo makePopupLink( '?view=monitor&amp;mid='.$monitor['Id'], 'zmMonitor'.$monitor['Id'], 'monitor', '<span class="'.$dclass.'">'.preg_replace( '/^.*@/', '', $monitor['Host'] ).'</span>', canEdit( 'Monitors' ) ) ?></div>
<?php } elseif ( $monitor['Type'] == "File" ) { ?>
            <div class="colSource"><?php echo makePopupLink( '?view=monitor&amp;mid='.$monitor['Id'], 'zmMonitor'.$monitor['Id'], 'monitor', '<span class="'.$dclass.'">'.preg_replace( '/^.*\//', '', $monitor['Path'] ).'</span>', canEdit( 'Monitors' ) ) ?></div>
<?php } elseif ( $monitor['Type'] == "Ffmpeg" || $monitor['Type'] == "Libvlc" ) {
    $domain = parse_url( $monitor['Path'], PHP_URL_HOST );
    $shortpath = $domain ? $domain : preg_replace( '/^.*\//', '', $monitor['Path'] );
    if ( $shortpath == '' ) {
        $shortpath = 'Monitor ' . $monitor['Id'];
    }
?>
            <div class="colSource"><?php echo makePopupLink( '?view=monitor&amp;mid='.$monitor['Id'], 'zmMonitor'.$monitor['Id'], 'monitor', '<span class="'.$dclass.'">'.$shortpath.'</span>', canEdit( 'Monitors' ) ) ?></div>
<?php } elseif ( $monitor['Type'] == "cURL" ) { ?>
            <div class="colSource"><?php echo makePopupLink( '?view=monitor&amp;mid='.$monitor['Id'], 'zmMonitor'.$monitor['Id'], 'monitor', '<span class="'.$dclass.'">'.preg_replace( '/^.*\//', '', $monitor['Path'] ).'</span>', canEdit( 'Monitors' ) ) ?></div>
<?php } else { ?>
            <div class="colSource">&nbsp;</div>
<?php } ?>
<?php if ( $show_storage_areas ) { ?>
			<div class="colStorage"><?php $Storage = new Storage( $monitor['StorageId'] ); echo $Storage->Name(); ?></div>
<?php } ?>
	</div><div class="Events">
<?php
    for ( $i = 0; $i < count($eventCounts); $i++ )
    {
?>
            <div class="colEvents <?php echo $eventCounts[$i]['title'] ?>"><?php echo makePopupLink( '?view='.$eventsView.'&amp;page=1'.$monitor['eventCounts'][$i]['filter']['query'], $eventsWindow, $eventsView, $monitor['EventCount'.$i], canView( 'Events' ) ) ?></div>
<?php
    }
?>
			</div>
            <div class="colZones"><?php echo makePopupLink( '?view=zones&amp;mid='.$monitor['Id'], 'zmZones', array( 'zones', $monitor['Width'], $monitor['Height'] ), $monitor['ZoneCount'], canView( 'Monitors' ) ) ?></div>
<?php
    if ( canEdit('Monitors') )
    {
?>
            <div class="colOrder"><?php echo makeLink( '?view='.$view.'&amp;action=sequence&amp;mid='.$monitor['Id'].'&amp;smid='.$seqIdUpList[$monitor['Id']], '<img src="'.$seqUpFile.'" alt="Up"/>', $monitor['Sequence']>$minSequence ) ?><?php echo makeLink( '?view='.$view.'&amp;action=sequence&amp;mid='.$monitor['Id'].'&amp;smid='.$seqIdDownList[$monitor['Id']], '<img src="'.$seqDownFile.'" alt="Down"/>', $monitor['Sequence']<$maxSequence ) ?></div>
<?php
    }
?>
            <div class="colMark"><input type="checkbox" name="markMids[]" value="<?php echo $monitor['Id'] ?>" onclick="setButtonStates( this );"<?php if ( !canEdit( 'Monitors' ) ) { ?> disabled="disabled"<?php } ?>/></div>
          </div>
<?php
}
?>
        </div>
      </div>
    </div>

<div id="footer">


<div class="pull-left">
<?php echo makePopupLink( '?view=bandwidth', 'zmBandwidth', 'bandwidth', $bwArray[$_COOKIE['zmBandwidth']], ($user && $user['MaxBandwidth'] != 'low' ) ) ?> <?php echo translate('BandwidthHead') ?>
</div>

<div class="pull-right">
	<?php echo makePopupLink( '?view=version', 'zmVersion', 'version', '<span class="'.$versionClass.'">v'.ZM_VERSION.'</span>', canEdit( 'System' ) ) ?>
</div>
<ul class="list-inline">
	<li><?php echo translate('Load') ?>: <?php echo getLoad() ?></li>
	<li><?php echo translate('Disk') ?>: <?php echo getDiskPercent() ?>%</li>
</ul>
</div> <!-- End .footer -->

    </form>
</body>
</html>
