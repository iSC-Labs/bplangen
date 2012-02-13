<?php

require_once('../lib/autoload.php');

$nodes = array();

//srand(0);

$buzz = new Buzz(rdir() .'/content/nouns.txt',rdir() .'/content/verbs.txt',rdir() .'/content/adjectives.txt');

function pick() {
	$args = func_get_args();
	return $args[rand(0,count($args)-1)];
}
$black = "bisque4";
$blue = "dodgerblue3";
$red = "firebrick3";
$links = array();
$nodes = array();
$has = array();
for($i=0;$i<rand(3,5);$i++) {
	$text = $buzz->words(rand(0,rand(2,3)));
	$nodename = "node$i";
	$shape = pick("box","oval","oval","ellipse","box","house","pentagon","diamond","egg","parallelogram","invtrapezium","folder","triangle");
	$fill = pick("transparent","transparent");
	$color = pick($black,$black,$black,$black,$red,$blue);
	$nodes[$nodename] = "$nodename [shape=$shape,fontcolor=$color, linewidth=4, style=filled,fillcolor=\"$fill\",label=\"$text\",fontsize=24.0];\n";
	
	if($i && rand(0,10)) {
		$prev = $i-1; 
		$links["$nodename.node$prev"] = "node$prev -> $nodename;\n";
	}
	for($j=0;$j<rand(0,1);$j++) {
		$less = rand(0,$i);
		$dir = pick('none','both','forward');
		$color = pick($black,$black,$red,$blue);
		$label = pick("","","",$buzz->words(1));
		$links["$nodename.node$less"] = "$nodename -> node$less [dir=$dir, color=$color,label=\"$label\",fontcolor=$color];\n";
	}
}

$g = "digraph G {
	graph [bb=\"0,0,300,300\", ratio=1, resolution=60,bgcolor=transparent];
	node [fontname=\"Comic Sans\",penwidth=4,color=\"$black\"];
	edge [penwidth=4,fontsize=24.0,fontname=\"Comic Sans\",color=\"$black\"];
	
	\n" . implode("\n",$nodes) . "\n" . implode("\n",$links) . " \n }" ;

$fn = tempnam('/tmp','gv');
file_put_contents($fn,$g);

$dots = array('/usr/local/bin/dot','/usr/bin/dot');
foreach($dots as $cdot) {
	if(file_exists($cdot)) {
		$dot = $cdot;
	}
}


if(!isset($_GET['i'])) {
	header("Content-Type: image/png");
	print `/usr/local/bin/dot $fn -Tpng `;
} else {
	print "<pre>";
	print $g;
	print "\n-----\n";
	print `/usr/local/bin/dot $fn`;
}
unlink($fn);
