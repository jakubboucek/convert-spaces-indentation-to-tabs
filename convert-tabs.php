<?php
$file = realpath($argv[1]);

echo str_replace(__DIR__, '', $file) . ": ";

$handle = fopen($file, "r");
try {
	$tabs = array();
	$line = 0;
	while (($buffer = fgets($handle, 4096)) !== false) {
		$line++;
		if(strlen($buffer) == 4096) {
			throw new Exception("Too long line in file", 1);
		}
		$buffer = rtrim($buffer);

		preg_match('/^ */', $buffer, $matches);
		$tab = strlen($matches[0]);
		if(($tab%2) && preg_match('/^ +\*/', $buffer)) {
			$tab--;
		}
		$tabs[$tab] = $line;
	}
	fclose($handle);
	$lines = $tabs;
	ksort($lines);
	$tabs = sanitizeTabs(array_keys($tabs));
	$tabSize = detectTabSize( $tabs );

	$content = file_get_contents($file);
	rsort($tabs);
	foreach($tabs as $tab) {
		$content = preg_replace('/^'.str_repeat(' ', $tab).'/m', str_repeat("\t", $tab/$tabSize), $content);
	}
	$content = preg_replace('/ *$/m', "", $content);
	file_put_contents($file, $content);
	echo "OK\n";
}
catch (Exception $e) {
	echo $e->getMessage()." " . json_encode($lines) . "\n";
}

function sanitizeTabs( $tabs ) {
	sort($tabs, SORT_NUMERIC);
	if($tabs[0] == 0) {
		array_shift( $tabs );
	}
	return $tabs;
}

function detectTabSize( $tabs ) {
	if(!$tabs) {
		return NULL;
	}
	$steps = $tabs[0];
	$prev = 0;
	foreach($tabs as $tab) {
		$step = abs($tab-$prev);
		$steps = min($steps, $tab, $step);
		$prev = $steps;
		if($steps == 1) {
			throw new Exception("File has invalid tabs (steps: 1, tabstop: $tab (prev: $prev))", 1);
		}
		if($tab%$steps) {
			throw new Exception("File has invalid tabs (no dividable, tabstop: $tab (prev: $prev, steps: $steps)", 1);
		}
	}
	return $steps;

}