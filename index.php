<?php
// ===============================================================================
// cPanel API 1 Doc

// https://documentation.cpanel.net/display/SDK/cPanel+API+1+Functions+-+Fileman%3A%3Afullbackup

// ===============================================================================
// cPanel API

include "lib/xmlapi.php";

// ===============================================================================
// SETTING

// ----------------------------------------------------------
// GLOBAL 

$emailNotify = '';
$backupDailyLimit = 5;

// ----------------------------------------------------------
// FTP 

$ftpHost = ""; 
$ftpUser = ""; 
$ftpPass = ""; 
$ftpRootPath = "/";
$ftpDailyDir = "daily";
$ftpMonthlyDir = "monthly";
$ftpYearlyDir = "yearly";

// ----------------------------------------------------------
// cPanel

$cpanelServer = ""; 
$cpanelUsername = ""; 
$cpanelPassword = "";

// ===============================================================================
// REMOTE FTP BACKUP OPERATIONS

$ftpConnection = ftp_connect($ftpHost);
$ftpLogin = ftp_login($ftpConnection, $ftpUser, $ftpPass);
 
if ((!$ftpConnection) || (!$ftpLogin)) {
	die("FTP CONNECTION ERROR");
}

// Checking FTP backup folders 
if (ftp_nlist($ftpConnection, $ftpRootPath . $ftpDailyDir) === false) {
	ftp_mkdir($ftpConnection, $ftpRootPath . $ftpDailyDir);	
}

if (ftp_nlist($ftpConnection, $ftpRootPath . $ftpMonthlyDir) === false) {
	ftp_mkdir($ftpConnection, $ftpRootPath . $ftpMonthlyDir);	
}

if (!ftp_nlist($ftpConnection, $ftpRootPath . $ftpYearlyDir) === false) {
	ftp_mkdir($ftpConnection, $ftpRootPath . $ftpYearlyDir);	
}

ftp_chdir($ftpConnection, $ftpRootPath . $ftpDailyDir);
$dailyBackup = ftp_nlist($ftpConnection, ".");

if (count($dailyBackup) >0 ){

	// Getting older and newer Backups files
	$olderFile = "";	
	$olderFileTimeStamp = 0;
	$newerFile = "";
	$newerFileTimeStamp = 0;

	foreach ($dailyBackup as $key => $file) {
		$fileTimeStamp = ftp_mdtm($ftpConnection, $file);
		if ($fileTimeStamp != -1){			
			if ($fileTimeStamp < $olderFileTimeStamp || $olderFileTimeStamp == 0){
				$olderFileTimeStamp = $fileTimeStamp;
				$olderFile = $file;
			}
			if ($fileTimeStamp > $newerFileTimeStamp || $newerFileTimeStamp == 0){
				$newerFileTimeStamp = $fileTimeStamp;
				$newerFile = $file;
			}
		
		}
	}

	// Delete Older Backup Done if reach daily limit 
	if (!empty($olderFile) && $backupDailyLimit > 1 && count($dailyBackup) == $backupDailyLimit){				
		ftp_delete($ftpConnection, $olderFile);
	}

	// Move newer Backup done to Monthly or Yearly Folder
	if(!empty($newerFile)){
		if (date("j") == 1 && date("n") == 1){		
			$newFileName =  date("Y", strtotime("-1 year"));
			ftp_rename($ftpConnection, $newerFile, "../". $ftpYearlyDir ."/". $newFileName . "_backup.tar.gz");
		}else if (date("j") == 1){	
			$newFileName =  date("Y_m", strtotime("-1 months"));
			ftp_rename($ftpConnection, $newerFile, "../". $ftpMonthlyDir ."/". $newFileName . "_backup.tar.gz");
		}
	}
}

 
// ===============================================================================
// CPANEL FULL BACKUP

$xmlapi = new xmlapi($cpanelServer);
$xmlapi->password_auth($cpanelUsername,$cpanelPassword);
$xmlapi->set_port('2083');
$xmlapi->set_output('json');

$apiArgs = array('passiveftp',$ftpHost,$ftpUser,$ftpPass,$emailNotify,21,$ftpRootPath.$ftpDailyDir);
$json = $xmlapi->api1_query($cpanelUsername,'Fileman','fullbackup',$apiArgs);
$result = json_decode($json,true);

if(!empty($result['data']['result'])){
	echo "ERROR: " . $result['data']['result'];
}

// ===============================================================================

?>
