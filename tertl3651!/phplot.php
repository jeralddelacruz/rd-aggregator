<?php
require_once "../sys/class.phplot.php";

session_start();

if(isset($_SESSION["PHPlot"][$_GET["type"]])){
	$plot=new PHPlot(800,600);

	$plot->SetImageBorderType("plain");
	$plot->SetPlotType("bars");
	$plot->SetDataType("text-data");
	$plot->SetDataValues($_SESSION["PHPlot"][$_GET["type"]]["data"]);
	$plot->SetTitle($_SESSION["PHPlot"][$_GET["type"]]["title"]);
	$plot->SetShading(0);
	$plot->SetLegend($_SESSION["PHPlot"][$_GET["type"]]["legend"]);
	$plot->SetLegendPixels(50,0);
	$plot->SetXTickLabelPos("none");
	$plot->SetXTickPos("none");

	unset($_SESSION["PHPlot"][$_GET["type"]]);

	$plot->DrawGraph();
}
exit;
?>