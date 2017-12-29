 #!/usr/bin/php
<?php
include './Core/BotCore.php';
/** EditInEditor.php 
* Opens a page in a user-defined editor and saves the page after closing the editor
* @Author KPFC 
* @Version 0.1
* @Status Alpha
*/ 
class BWarchivierung extends Core { 
	public function BWarchivierung ($Account, $Job, $pUseHTTPS = true) { 
		$this->initcurl($Account, $Job, $pUseHTTPS = true);	//login
		//ask for editor to use
	//	$editor = $this->askOperator('Enter your favorit text editor:');
	//	if (!$editor) {
			$editor = 'kate'; //change this as you like
	//	}
		while (!$exit) {
			$pagename = $this->askOperator('Enter the page to edit:');
			$pagecontent = $this->readPage($pagename);
			//create temporary file to edit
			$page_tmp = tempnam(__DIR__, '.tmp_EiE_');
			fwrite(fopen($page_tmp, 'a+'), $pagecontent);
			popen($editor . ' ' . $page_tmp, "w");
			$this->askOperator('Done?');
			$pagecontent = file_get_contents($page_tmp);
			//delete temporary file
			unlink($page_tmp);
			//save page
			$minor = $this->askOperator('Minor? [y/N/a]:');
			if (($minor==='a') || ($minor==='A')) {
				echo "aborted edit …\n";
			} else if (($minor==='y') || ($minor==='Y') || ($minor==='yes') || ($minor==='Yes')) {
				$this->editPageMinor($pagename, $pagecontent, $this->askOperator('Edit Summary:'));
				echo "saved minor edit …\n";
			} else {
				$this->editPage ($pagename, $pagecontent, $this->askOperator('Edit Summary:'));
				echo "saved edit …\n";
			}
			$continue = $this->askOperator("Continue? [y/N]");
			if (($continue==='y') || ($continue==='Y') || ($continue==='yes') || ($continue==='Yes')) {
				$exit = false;
			} else {
				$exit = true;
			}
			
		}
	}
}
$Bot = new BWarchivierung ('KPFC@dewiki', 'Custom-Edit'); // Aufrufen der definierten Passwortdaten in Password.php
?>
