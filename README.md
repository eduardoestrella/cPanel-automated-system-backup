# cPanel Automated Backup System CP-ABS

Automated full Backups dialy with a Cron task and upload it to a remote FTP using orginize folder structure by days,months and years. 

cPanel is a control panel base on Linux designed to simplify the process of hosting a web site. Most of professional web hosting provide cPanel. CP-ABS can be used with all of then that use cPanel API 1. Some of providers are GoDaddy, blueHost, unitedHosting, HostGator, resellerclub, etc. (http://cpanel.net/hosting-providers/hosting-provider-showcase/)

Developed in PHP scripting language.

# Dependencies

We need a xmlapi (https://github.com/CpanelInc/xmlapi-php) to connect with cPanel's XML-API.

# Features

- FullBackup from cPanel (mySQL, data, emails, configuration...)
- Remote FTP upload to a server when cPanel backup finish
- Automated directory structure for Dialy backup 
- Automated directory structure for Monthly backup
- Automated directory structure for Yearly backup
- Automated backup delete when reach file limit for Dialy Directory

# How to use

Configure the defaults settings for FTP and cPanel accounts. Editing index.php file:

```$emailNotify = '';```   Notification email for finish backup
```$backupDailyLimit = 5;``` Number of store backups in daily directory

```$ftpHost = "";``` FTP remote IP or domain
```$ftpUser = "";``` FTP user account
```$ftpPass = "";``` FTP password account
```$ftpRootPath = "/";``` FTP path where deploy directory structure
```$ftpDailyDir = "daily";``` Store daily Backups
```$ftpMonthlyDir = "monthly";``` Store Monthly backups
```$ftpYearlyDir = "yearly";``` Store Yearly backups

```$cpanelServer = "";``` cPanel Server IP or domain name (cpanel.domain.com)
```$cpanelUsername = "";``` cPanel User Account
```$cpanelPassword = "";``` cPanel Password Account

Configure a cron task in your web hosting to run CP-ABS script every day.

When it finish, cPanel will send a email notification with the log of backup and upload FTP. Then you could find the backup in the directories structure.

# Tips

- There is new cPanel API 2 but it hasn't got backup Feature, so I use API 1 to do it.
- The day 1 of each month, last done backup will move to Monthly Directory. I do that because FTP protocol don't allow ```copy``` commands from FTP server to same FTP server, I would use local server to ```get``` and ```put``` the file but this would waste too much time transfering a FullBackup.
- The day 1 of January of each year, last done backup will move to Yearly Directory. The reason is the same of the monthly backup.
