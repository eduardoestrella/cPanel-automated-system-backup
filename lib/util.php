<?php

function ftp_isdir($connect_id,$dir)
{
    if(@ftp_chdir($connect_id,$dir))
    {
        ftp_cdup($connect_id);
        return true;
    }
    else
    {
        return false;
    }
}

/*
get all files with cPanel format: "backup-[DATE]_[CPANEL_USER].tar"
*/
function getCpanelBackups($fileList){

    for($i=0;$i<count($fileList);$i++){
        if(substr( $fileList[$i], 0, 7 ) != "backup-"){
            unset($fileList[$i]);
        }
    }

    return $fileList;
}

?>