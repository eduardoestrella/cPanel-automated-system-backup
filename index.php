<?php
// ===============================================================================
// cPanel API 1 Doc

// https://documentation.cpanel.net/display/SDK/cPanel+API+1+Functions+-+Fileman%3A%3Afullbackup

// ===============================================================================
// DEPENDENCIES

// cPanel API
include('lib/xmlapi.php');
// Fubtion Utils
include('lib/util.php');

// ===============================================================================
// CONFIG

include('config.php');

// ===============================================================================
// REMOTE FTP BACKUP OPERATIONS

logger("cPanel automated system backup - Version ".VERSION);

// FTP Connection
$ftpConnection = ftp_connect(FTP_HOST,FTP_PORT);

if (!$ftpConnection) {
	logger("Couldn't connect to [" . FTP_HOST. ":". FTP_PORT."]");
} else {
	logger("Connected to [". FTP_HOST . ":". FTP_PORT."]");
}

// FTP Login and set passive mode
$ftpLogin = ftp_login($ftpConnection, FTP_USER, FTP_PASS);
ftp_pasv($ftpConnection, true);

if (!$ftpLogin) {
	logger("Login Error ");
} else {
	logger("User [".FTP_USER . "] Logged");
}

// Checking FTP backup folders
if (ftp_isdir($ftpConnection, FTP_ROOT_PATH . FTP_DAILY_DIRNAME) === false) {
	ftp_mkdir($ftpConnection, FTP_ROOT_PATH . FTP_DAILY_DIRNAME);
	logger("Folder ". FTP_ROOT_PATH . FTP_DAILY_DIRNAME. " created");
}

if (ftp_isdir($ftpConnection, FTP_ROOT_PATH . FTP_MONTHLY_DIRNAME) === false) {
	ftp_mkdir($ftpConnection, FTP_ROOT_PATH . FTP_MONTHLY_DIRNAME);
	logger("Folder ". FTP_ROOT_PATH . FTP_MONTHLY_DIRNAME. " created");
}

if (ftp_isdir($ftpConnection, FTP_ROOT_PATH . FTP_YEARLY_DIRNAME) === false) {
	ftp_mkdir($ftpConnection, FTP_ROOT_PATH . FTP_YEARLY_DIRNAME);
	logger("Folder ". FTP_ROOT_PATH . FTP_YEARLY_DIRNAME. " created");
}


// Managing Daily, Monthly and Yearly Backups
ftp_chdir($ftpConnection, FTP_ROOT_PATH . FTP_DAILY_DIRNAME);
$dailyBackup = ftp_nlist($ftpConnection, ".");

if (count($dailyBackup) >0 ){

	logger("Num backups found: " . count($dailyBackup));

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
	if (!empty($olderFile) && BACKUP_DAILY_LIMIT > 1 && count($dailyBackup) == (BACKUP_DAILY_LIMIT)){
		logger("Deleting [" . $olderFile . "] file. Daily backup limits reached. Limit set to " . BACKUP_DAILY_LIMIT . " backups");
		ftp_delete($ftpConnection, $olderFile);
	}

	// Move newer Backup done to Monthly or Yearly Folder
	if(!empty($newerFile)){
	    date_default_timezone_set("UTC");
		if (date("j") == 1 && date("n") == 1){
			$newFileName =  date("Y", strtotime("-1 year"));
			logger("Moving yearly backup file [". $newFileName . "_backup.tar.gz] to folder [".FTP_YEARLY_DIRNAME."]");
			ftp_rename($ftpConnection, $newerFile, "../". FTP_YEARLY_DIRNAME."/". $newFileName . "_backup.tar.gz");
		}else if (date("j") == 1){
			$newFileName =  date("Y_m", strtotime("-1 months"));
			logger("Moving monthly backup file [". $newFileName . "_backup.tar.gz] to folder [".FTP_MONTHLY_DIRNAME."]");
			ftp_rename($ftpConnection, $newerFile, "../". FTP_MONTHLY_DIRNAME ."/". $newFileName . "_backup.tar.gz");
		}
	}
} else {
    logger("Daily Backup not Found in remote FTP [".FTP_ROOT_PATH . FTP_DAILY_DIRNAME."] folder");
}

// close the connection
ftp_close($ftpConnection);

// ===============================================================================
// CPANEL FULL BACKUP

$xmlapi = new xmlapi(CPANEL_HOST);
$xmlapi->password_auth(CPANEL_USER,CPANEL_PASS);
$xmlapi->set_port('2083');
$xmlapi->set_output('json');

$apiArgs = array('passiveftp',FTP_HOST,FTP_USER,FTP_PASS,EMAIL_NOTIFICATION,FTP_PORT,FTP_ROOT_PATH.FTP_DAILY_DIRNAME);
$json = $xmlapi->api1_query(CPANEL_USER,'Fileman','fullbackup',$apiArgs);
$result = json_decode($json,true);

if(!empty($result['data']['result'])){
	logger( "API cPanel FullBackup Error: " . $result['data']['result']);
} else {
	logger("API cPanel FullBackup launched");
	logger("Creating and Uploading new Backup to [".FTP_HOST.":".FTP_PORT."]");
	logger("When finished the upload to remote FTP an email notification will be send to [".EMAIL_NOTIFICATION."]");
}

// ===============================================================================

?>
