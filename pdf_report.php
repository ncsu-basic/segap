<?php
require('fpdf.php');
require('se_config.php');
pg_connect($pg_connect);
set_time_limit(300);

//set mapfile and load mapscript if not already loaded
$mapfile = "/var/www/html/segap/segap.map";

//var_dump($_POST);

//get form variables
$win_w = $_POST['win_w'];
$win_h = $_POST['win_h'];
$extent_raw = $_POST['extent'];
$mode = $_POST['mode'];
$layer = $_POST['layers2'];
$state_aoi = $_POST['state'];
$owner_aoi = $_POST['owner'];
$manage_aoi = $_POST['manage'];
$status_aoi = $_POST['status'];
$county_aoi = $_POST['county'];
$basin_aoi = $_POST['basin'];
$bcr_aoi = $_POST['bird_consv'];
$lcc_aoi = $_POST['lcc'];
$ecosys_aoi = $_POST['ecosys'];
$desc = $_POST['desc'];
$dpi = $_POST['dpi'];
$species_layer = $_POST['species_layer'];
$map_species = $_POST['map_species'];
$aoi_name = $_POST['aoi_name'];
$sppcode = $_POST['sppcode'];

//create click obj for zoom
$click_point = ms_newPointObj();
$click_x=$win_w/2;
$click_y=$win_h/2;
$click_point->setXY($click_x, $click_y);

//save extent to rect and create rectobj for zoom
$extent = explode(" ", $extent_raw);
$old_extent =  ms_newRectObj();
$old_extent->setextent($extent[0], $extent[1], $extent[2], $extent[3]);


//create map object
$map = ms_newMapObj($mapfile);

//set layers
if(preg_match("/cities/", $layer)){
	$this_layer = $map->getLayerByName('urban');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('urban');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/counties/", $layer)){
	$this_layer = $map->getLayerByName('counties');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('counties');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/roads/", $layer)){
	$this_layer = $map->getLayerByName('roads');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('roads');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/hydro/", $layer)){
	$this_layer = $map->getLayerByName('rivers');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('rivers');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/elevation/", $layer)){
	$this_layer = $map->getLayerByName('elevation');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('elevation');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/landcover/", $layer)){
	$this_layer = $map->getLayerByName('landcover');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('landcover');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/states/", $layer)){
	$this_layer = $map->getLayerByName('states');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('states');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/wtshds/", $layer)){
	$this_layer = $map->getLayerByName('watersheds');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('watersheds');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/bcr/", $layer)){
	$this_layer = $map->getLayerByName('bcr');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('bcr');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/lcc/", $layer)){
//if(true){
	$this_layer = $map->getLayerByName('lcc');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('lcc');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/ownership/", $layer)){
	$this_layer = $map->getLayerByName('gapown');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('gapown');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/management/", $layer)){
	$this_layer = $map->getLayerByName('gapman');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('gapman');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/status/", $layer)){
	$this_layer = $map->getLayerByName('gapsta');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('gapsta');
	$this_layer->set('status', MS_OFF);
}
if (isset($county_aoi) && !empty($county_aoi)){
	$key_gap = explode(":", $county_aoi);
	$filter = "(ogc_fid = {$key_gap[0]})";
	for($i=1; $i<count($key_gap); $i++){
		$filter .= " or (ogc_fid = {$key_gap[$i]})";
	}
	$this_layer = $map->getLayerByName('counties_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}
if (isset($basin_aoi) && !empty($basin_aoi)){
	$key_gap = explode(":", $basin_aoi);
	$filter = "(ogc_fid = {$key_gap[0]})";
	for($i=1; $i<count($key_gap); $i++){
		$filter .= " or (ogc_fid = {$key_gap[$i]})";
	}
	$this_layer = $map->getLayerByName('basin_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}
if (isset($bcr_aoi) && !empty($bcr_aoi)){
	$key_gap = explode(":", $bcr_aoi);
	$filter = "(ogc_fid = {$key_gap[0]})";
	for($i=1; $i<count($key_gap); $i++){
		$filter .= " or (ogc_fid = {$key_gap[$i]})";
	}
	//echo $filter;
	$this_layer = $map->getLayerByName('bcr_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}
if (isset($lcc_aoi) && !empty($lcc_aoi)){
	$key_gap = explode(":", $lcc_aoi);
	$filter = "(gid = {$key_gap[0]})";
	for($i=1; $i<count($key_gap); $i++){
		$filter .= " or (gid = {$key_gap[$i]})";
	}
	//echo $filter;
	$this_layer = $map->getLayerByName('lcc_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}
if (isset($state_aoi) && !empty($state_aoi)){
	$key_gap = explode(":", $state_aoi);
	$filter = "(ogc_fid = {$key_gap[0]})";
	for($i=1; $i<count($key_gap); $i++){
		$filter .= " or (ogc_fid = {$key_gap[$i]})";
	}
	//echo $filter;
	$this_layer = $map->getLayerByName('state_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}
if (isset($owner_aoi) && !empty($owner_aoi)){
	$key_gap = explode(":", $owner_aoi);
	$key_gap_explode = explode("|",$key_gap[0]);
	$filter = "(state_fips = {$key_gap_explode[0]} and own_c_recl = {$key_gap_explode[1]})";
	for($i=1; $i<count($key_gap); $i++){
		$key_gap_explode = explode("|",$key_gap[$i]);
		$filter .= " or (state_fips = {$key_gap_explode[0]} and own_c_recl = {$key_gap_explode[1]})";
	}
	//echo $filter;
	$this_layer = $map->getLayerByName('owner_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}
if (isset($manage_aoi) && !empty($manage_aoi)){
	$key_gap = explode(":", $manage_aoi);
	$key_gap_explode = explode("|",$key_gap[0]);
	$filter = "(state_fips = {$key_gap_explode[0]} and man_c_recl = {$key_gap_explode[1]})";
	for($i=1; $i<count($key_gap); $i++){
		$key_gap_explode = explode("|",$key_gap[$i]);
		$filter .= " or (state_fips = {$key_gap_explode[0]} and man_c_recl = {$key_gap_explode[1]})";
	}
	$this_layer = $map->getLayerByName('manage_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}
if (isset($status_aoi) && !empty($status_aoi)){
	$key_gap = explode(":", $status_aoi);
	$key_gap_explode = explode("|",$key_gap[0]);
	$filter = "(state_fips = {$key_gap_explode[0]} and status_c = {$key_gap_explode[1]})";
	for($i=1; $i<count($key_gap); $i++){
		$key_gap_explode = explode("|",$key_gap[$i]);
		$filter .= " or (state_fips = {$key_gap_explode[0]} and status_c = {$key_gap_explode[1]})";
	}
	$this_layer = $map->getLayerByName('status_select');
	$this_layer->setFilter($filter);
	$this_layer->set('status', MS_ON);
}
if (isset($ecosys_aoi) && !empty($ecosys_aoi)){
	$this_layer = $map->getLayerByName('ecosys_select');
	$this_layer->set('status', MS_ON);
}

//convert sppcode to sql
$rangemap = "r_".strtolower($sppcode);
$data = "wkb_geometry from ".$rangemap;

//set raster to display species maps
if(preg_match("/range/", $species_layer)){
$this_layer = $map->getLayerByName('rangemaps');
$this_layer->set('data', $data);
$this_layer->set('status', MS_ON);
$this_layer->set('opacity', 50);
//turn off other rasters
//set layers from controls
if(preg_match("/landcover/", $layer)){
	$this_layer = $map->getLayerByName('landcover');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('landcover');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/elevation/", $layer)){
	$this_layer = $map->getLayerByName('elevation');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('elevation');
	$this_layer->set('status', MS_OFF);
}
}


if(preg_match("/habitat|ownership|status|manage|richness/", $species_layer)){
$this_layer = $map->getLayerByName('mapcalc');
$this_layer->set('data', $grass_raster.$map_species);
//echo $grass_raster.$map_species;
$this_layer->set('status', MS_ON);
//turn off other rasters
$this_layer = $map->getLayerByName('elevation');
$this_layer->set('status', MS_OFF);
$this_layer = $map->getLayerByName('landcover');
$this_layer->set('status', MS_OFF);
}

//convert sppcode to raster name
$raster = "d_".strtolower($sppcode);

if(preg_match("/predicted/", $species_layer)){
$this_layer = $map->getLayerByName('mapcalc');
//echo $grass_raster_perm.$raster;
$this_layer->set('data', $grass_raster_perm.$raster);
$this_layer->set('status', MS_ON);
$this_layer->set('opacity', 50);
//set layers from controls
if(preg_match("/landcover/", $layer)){
	$this_layer = $map->getLayerByName('landcover');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('landcover');
	$this_layer->set('status', MS_OFF);
}
if(preg_match("/elevation/", $layer)){
	$this_layer = $map->getLayerByName('elevation');
	$this_layer->set('status', MS_ON);
}else{
	$this_layer = $map->getLayerByName('elevation');
	$this_layer->set('status', MS_OFF);
}

}

//draw AOI outline
$filter = "(name = '{$aoi_name}')";
$this_layer = $map->getLayerByName('aoi');
$this_layer->setFilter($filter);
$this_layer->set('status', MS_ON);
//create map for pdf

$pdfmapname = "map".rand(0,9999999).".png";
//$mspath = "/data/server_temp/";

$pdfmaploc = "{$mspath}{$pdfmapname}";
if ($dpi == 300) {
	$map->setSize(3000, 1800);
	$map->scalebar->set("width", 400);
	$map->scalebar->set("height", 8);
	$map->scalebar->label->set("size", 32);
	$map->getLayerByName('rivers')->getClass(0)->label->set("size", 24);
	$map->getLayerByName('roads')->getClass(0)->label->set("size", 24);

} else {
	$map->setSize(720, 432);
}


$map->zoompoint(1, $click_point, $win_w, $win_h, $old_extent);

$pdfmapimage = $map->draw();
$pdfmapimage->saveImage($pdfmaploc);



//////////////////////////////////////////////////////////////



class PDF extends FPDF
{
	function Footer()
	{
		$this->Image('/var/www/html/graphics/segap/USGS_GAP_BaSIC_PDF_Logo_SE.png',0.5,7.5,0,0.5);
	}
}

//Instanciation of inherited class
$pdf=new PDF('L','in', 'Letter');
$pdf->SetFont('Arial','B',24);
$pdf->SetMargins(0.5,0.5);
$pdf->AddPage();


//print title
//$pdf->Cell(3);
$pdf->Cell(0,0,$desc,0,0);

//output map
$pdf->Image($mspath.$pdfmapname,0.5,1.25,10,6);

//add legends page

if((preg_match("/landcover/", $layer)   && (strlen($species_layer) == 0)) ||  preg_match("/habitat/", $species_layer))
{
	$pdf->AddPage();
	$pdf->Cell(0,0,'GAP Land Cover',0,0);
	$pdf->Image('/var/www/html/graphics/segap/se_lcov.png',0.5,1.25,10,0);
}
if(preg_match("/elevation/", $layer) && (strlen($species_layer) == 0)){
		$pdf->AddPage();
		$pdf->Cell(5,0,'Elevation (meters)',0,0);
		$pdf->Image('/var/www/html/graphics/segap/se_elev_legend.png',0.5,1,0,6);
}
if(preg_match("/management/", $layer) || preg_match("/manage/", $species_layer) ){
	$pdf->AddPage();
	$pdf->Cell(5,0,'Management (Stewardship)',0,0);
	$pdf->Image('/var/www/html/graphics/segap/se_manage_legend.png',0.5,1,0,6);
}
if(preg_match("/ownership/", $layer) || preg_match("/ownership/", $species_layer)){
	$pdf->AddPage();
	$pdf->Cell(5,0,'Ownership (Stewardship)',0,0);
	$pdf->Image('/var/www/html/graphics/segap/se_owner_legend.png',0.5,1,0,6);
}
if(preg_match("/status/", $layer)  || preg_match("/status/", $species_layer)){
	$pdf->AddPage();
	$pdf->Cell(5,0,'GAP Status (Stewardship)',0,0);
	$pdf->Image('/var/www/html/graphics/segap/se_status_legend.png',0.5,1,6,0);
}

if(preg_match("/range/", $species_layer)){
$pdf->AddPage();
$pdf->Cell(0,0,'Range legend',0,0);
$pdf->Image('/var/www/html/graphics/segap/range_leg.png',0.5,1.25,1.8,0);}

if(preg_match("/predicted/", $species_layer)){
$pdf->AddPage();
$pdf->Cell(0,0,'Predicted legend',0,0);
$pdf->Image('/var/www/html/graphics/segap/predicted_leg.png',0.5,1.25,1.8,0);}

$file_name = "segap".rand(1,1000).".pdf";
$pdf->Output($file_name, I);

?>
