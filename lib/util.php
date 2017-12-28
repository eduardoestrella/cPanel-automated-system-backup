<?php

function logger($message){
   if(DEBUG) echo $message. "</br>";
}

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


?>