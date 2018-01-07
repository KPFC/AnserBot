#!/usr/bin/php
<?php
include './Core/BotCore.php';

/** SortCat.php
* Sortiere eine Kategorie.
* Nach MoveIt.php von Freddy2001 <freddy2001@wikipedia.de>
* @Author KPFC
* @Version 0.1
* @Status Alpha
*/
class SortCat extends Core {
	public function SortCat ($Account, $Job, $pUseHTTPS = true) {
		$this->initcurl($Account, $Job, $pUseHTTPS = true);

		// Bitte hier die Kategorie, die sortiert werden soll angeben
		$cat = "Pink roses";
		// Bitte hier die neue Kategorie angeben
		$target = "Category:" . $cat;
		echo  "---SortCat---\nDurchlauf: " . date('l jS F Y H:i:s') . "\nTarget: " . $target . "---\n";
		$pages = $this->getCatMembers($target, true, true);	
		print_r($pages);
		for($i = 0; count($pages) > $i; $i++) {
			$page = $pages[$i];
			$pagecontent = $this->readPage($page);
			$oldpagecontent = $pagecontent;
			echo "\n" . $page . "\n";
			$page = str_replace("Category:", "", $page);
			//things to be trimmed
			$page = str_replace("Rosa", "", $page);
			$page = trim($page);
			$page = trim($page, '\'');
			$page = str_replace('\' (', " ", $page);
			$page = str_replace("(","", $page);
			$page = str_replace(")", "", $page);
		/*	$page = str_replace("ä", "a", $page);
			$page = str_replace("ö", "o", $page);
			$page = str_replace("ü", "u", $page);	*/
			$page = trim($page);
			//either sort one category by another or use defaultsort
			//if the category is already sorted it skips; if not, it will use DEFAULTSORT; if this is already with another sorting, it will use a |-sorting
			if (!(strstr($pagecontent, "Category:" . $cat . "|" . $page)||strstr($pagecontent, "{{DEFAULTSORT:$page"))&&strstr($pages[$i], "Rosa")) {
				if (strstr($pagecontent, "{{DEFAULTSORT:")) {
					preg_match("~({{DEFAULTSORT:.*}})~", $pagecontent, $dfs);
					echo "-: " . $dfs[1] . "\n";
					echo "+: {{DEFAULTSORT:" . $page . "}}\n";
					$ds = $this->askOperator("Use new defaultsort? [y/N/a/q]");
					if ($ds==='y') {
						$pagecontent = str_replace($dfs[1] . "\n", "", $pagecontent);
						$pagecontent = "{{DEFAULTSORT:" . $page . "}}\n" . $pagecontent;
					} else if ($ds==='q') {
						break;
					} else if ($ds!=='a') {
						$pagecontent = str_replace ("[[Category:" . $cat . "]]", "[[Category:" . $cat . "|" . $page . "]]", $pagecontent);
					}
				} else {
					$pagecontent = "{{DEFAULTSORT:" . $page . "}}\n" . $pagecontent;
				}
			}
			if ($pagecontent!==$oldpagecontent) {
				$summary = "Sort";
				echo "\n---\n";
				print_r($oldpagecontent);
				echo "\n+++\n";
				print_r($pagecontent);
				$cont = $this->askOperator("\nsave? [y/N/q]");
				if ($cont==='q') {
					break;
				} else if ($cont==='y') {
					$this->editPageMinor($pages[$i], $pagecontent, $summary);
					sleep(0);
				}
			}
		}
	}
}

$SortCat = new SortCat ('KPFC@commonswiki', 'SortCat'); // Aufrufen der definierten Passwortdaten in Password.php
?>
