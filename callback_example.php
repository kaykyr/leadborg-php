<?php
	include "LeadBorg.class.php";
	
	$robot = new LeadBorg("/*Paste your token here*/");

	$robot->onReceiveMessage(function($that, $message){
        	switch ($message->type) {
            		case 'chat':
                		sleep(rand(5,10));
                		$that->sendMessage($message->chatId->user, $message->content); //echo the message
                		break;

            		case 'ptt':
                		$that->sendMessage($message->chatId->user, "I can't hear you now... 🙉"));
                		break;
        	}
	});
