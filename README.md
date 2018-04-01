# cPanel Automated System Backup

CP-ASB (cPanel Automated System Backup) automated full Backups daily with a Cron task and upload it to a remote FTP using orginize folder structure by days,months and years. 

cPanel is a control panel base on Linux designed to simplify the process of hosting a web site. Most of professional web hosting provide cPanel. CP-ASB can be used with all of then that use cPanel API 1. Some of [providers](http://cpanel.net/hosting-providers/hosting-provider-showcase/) are GoDaddy, blueHost, unitedHosting, HostGator, resellerclub, etc. 

Developed in PHP scripting language.

# Version

Version 1.2 - Released 01.04.2018 - [ChangeLog](https://github.com/eduardoestrella/cPanel-automated-system-backup/blob/master/changelog.txt)

# Dependencies

- [xmlapi](https://github.com/CpanelInc/xmlapi-php) to connect with cPanel's XML-API.

# Features

- FullBackup from cPanel (mySQL, data, emails, configuration...)
- Remote FTP upload to a server when cPanel backup finish
- Automated directory structure for Daily backup 
- Automated directory structure for Monthly backup
- Automated directory structure for Yearly backup
- Automated backup delete when reach file limit for Daily Directory

# How to use

1. Configure the defaults settings for FTP and cPanel accounts. Editing config.php file:
  - ```EMAIL_NOTIFICATION```   Notification email for finish backup
  - ```BACKUP_DAILY_LIMIT``` Number of store backups in daily directory
  - ```FTP_HOST``` FTP remote IP or domain
  - ```FTP_PORT``` FTP remote Port
  - ```FTP_USER``` FTP user account
  - ```FTP_PASS``` FTP password account
  - ```FTP_ROOT_PATH``` FTP path where deploy directory structure
  - ```FTP_DAILY_DIRNAME``` Store daily Backups
  - ```FTP_MONTHLY_DIRNAME``` Store Monthly backups
  - ```FTP_YEARLY_DIRNAME``` Store Yearly backups
  - ```CPANEL_HOST``` cPanel Server IP or domain name (cpanel.domain.com)
  - ```CPANEL_USER``` cPanel User Account
  - ```CPANEL_PASS``` cPanel Password Account
  - ```DEBUG``` Enable Debug Mode

2. Upload index.php and lib folder to your web hosting.
3. Configure a cron task in your web hosting to run index.php script every day.

# Tips

- There is new cPanel API 2 but it hasn't got backup Feature, so I use API 1 to do it.
- The day 1 of each month, last done backup will move to Monthly Directory. I do that because FTP protocol don't allow ```copy``` commands from FTP server to same FTP server, I would use local server to ```get``` and ```put``` the file but this would waste too much time transfering a FullBackup. It mean last backup daily file for each month will move to Monthly directory.
- The day 1 of January of each year, last done backup will move to Yearly Directory. The reason is the same of the monthly backup. it mean last backup monthly file for each year will move to Year directory.
- When Backup finish, cPanel will send a email notification with the log of backup and upload FTP. Then you could find the backup in the directories structure.
- A log file is created in Logs folder. Enabled DEBUG in config.php if you want more detail in log file.

# Debug

Since version 1.1 you can show in the browser the flow of the script. To enable Debug mode set to ```true``` the Define variable ```DEBUG``` in ```config.php``` file and force the execution of index.php manually instead of cron.

# Developed By

* Eduardo Estrella Rosario - <eduardo.estrella.rosario@gmail.com> - <a href="https://www.linkedin.com/in/eduardoestrella"><img alt="Add me to Linkedin" src="https://image.freepik.com/iconos-gratis/boton-del-logotipo-linkedin_318-84979.png" height="15" width="15"/></a>


# License

![GPLv3](https://upload.wikimedia.org/wikipedia/commons/thumb/9/93/GPLv3_Logo.svg/200px-GPLv3_Logo.svg.png)
