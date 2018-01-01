#!/usr/bin/php
<?php
require './Core/BotCore.php';
/** AnserWikiBrowser.php
* Opens a page in a user-defined editor and let’s you do things.
* @Author KPFC
* @Version 0.1
* @Status Alpha
*/
class AnserWikiBrowser extends Core {
	public function __construct ($Account, $Job, $pUseHTTPS = true) {
		$this->initcurl($Account, $Job, $pUseHTTPS); //login
		//ask for editor to use
		$editor = $this->askOperator('Enter your favorit text editor:');
		if (!$editor) {
			$editor = 'code'; //change this as you like
		}
		$exit = false;
		$pagename = $this->askOperator('Enter the page to view:');
		//create temporary file to edit
		$page_tmp = tempnam(__DIR__, 'AnserBrowser_');
		while (!$exit) {
			$pagecontent = $this->readPage($pagename);
			$action = "read";
			$head[0] = "/* HEAD\n * PAGE: $pagename\n * ACTION: $action\n**/\n";
			fwrite(fopen($page_tmp, 'a+'), $head[0] . $pagecontent);
			if ($editor !== 'vi') {
				popen($editor . ' ' . $page_tmp, "w");
			} else {
				echo "Please open $page_tmp with vi.";
			}
			echo "Replace \"ACTION: $action\" with the action you want to perform.\n";
			echo "Replace \"PAGE: $pagename\" with the page you want to perform the action on.\n";
			$action = $this->askOperator('Done?');
			//read what to do from new file content
			preg_match("~/\* HEAD\n \* PAGE: (.*)\n \* ACTION: (.*)\n\*\*/\n~", file_get_contents($page_tmp), $head);
			$pagecontent = substr(file_get_contents($page_tmp), strlen($head[0]));
			if ($action === "") {
				$action = $head[2];
			}
			//save page
			switch ($action) {
				case "edit":
				case "save":
				case "e":
					$this->editPage ($pagename, $pagecontent, $this->askOperator('Edit Summary:'));
					echo "saved edit …\n";
					break;
				case "read":
				case "r":
					break;
				case "history": //here has to be more
				case "h":
					break;
				case "move":
				case "m":
					$this->movePage ($pagename, $this->askOperator('New Pagename:'), $this->askOperator('Reason:'));
					break;
				case "purge":
				case "*":
					$this->purge ($pagename);
					break;
				case "watch":
				case "w":
					$this->watch ($pagename);
					break;
				case "delete":
				case "d":
					$this->deletePage ($pagename, $this->askOperator('Reason:'));
					break;
				case "protect":
				case "p":
					$this->protectPage ($pagename, $this->askOperator('Reason:'), $this->askOperator('Protections:'), $this->askOperator('Expiry:'), $this->askOperator('Cascade:'));
					break;
				case "quit":
				case "exit":
				case "q":
					$exit = true;
					break;
			}
			$pagename = $head[1];
		}
		//delete temporary file
		unlink($page_tmp);
	}
}
$Bot = new AnserWikiBrowser('KPFC@test2wiki', 'Custom-Edit'); // Aufrufen der definierten Passwortdaten in Password.php
?>
